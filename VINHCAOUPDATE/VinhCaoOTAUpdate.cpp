#include "VinhCaoOTAUpdate.h"

OTAUpdate::OTAUpdate(const char* serverURL, const String& currentVersion)
    : _serverURL(serverURL), _currentVersion(currentVersion) {}

void OTAUpdate::checkForUpdates() {
    if (WiFi.status() == WL_CONNECTED) {
        HTTPClient http;
        String url = String(_serverURL) + "?version=" + _currentVersion;
        http.begin(url);

        int httpCode = http.GET();
        if (httpCode == HTTP_CODE_OK) {
            String payload = http.getString();
            Serial.println("Server response: " + payload);

            DynamicJsonDocument doc(1024);
            DeserializationError error = deserializeJson(doc, payload);
            if (!error) {
                bool updateAvailable = doc["update"];
                if (updateAvailable) {
                    String newVersion = doc["version"];
                    String firmwareURL = doc["url"];

                    Serial.println("New version available: " + newVersion);
                    Serial.println("Downloading firmware from: " + firmwareURL);

                    if (downloadFirmware(firmwareURL)) {
                        Serial.println("Update successful. Rebooting...");
                        ESP.restart();
                    } else {
                        Serial.println("Update failed.");
                    }
                } else {
                    Serial.println("No updates available.");
                }
            } else {
                Serial.println("Failed to parse server response.");
            }
        } else {
            Serial.printf("Failed to connect to server. HTTP code: %d\n", httpCode);
        }
        http.end();
    } else {
        Serial.println("WiFi not connected!");
    }
}

bool OTAUpdate::downloadFirmware(const String& url) {
    HTTPClient http;
    http.begin(url);

    int httpCode = http.GET();
    if (httpCode == HTTP_CODE_OK) {
        int contentLength = http.getSize();
        WiFiClient* stream = http.getStreamPtr();

        if (Update.begin(contentLength)) {
            Serial.println("Start updating firmware...");
            size_t written = 0;
            uint8_t buffer[512];

            while (http.connected() && written < contentLength) {
                size_t size = stream->available();
                if (size > 0) {
                    int bytes = stream->readBytes(buffer, size > sizeof(buffer) ? sizeof(buffer) : size);
                    written += Update.write(buffer, bytes);

                    int progress = (written * 100) / contentLength;
                    Serial.printf("Progress: %d%%\n", progress);
                }
                delay(1);
            }

            Serial.println(); // Xuống dòng sau tiến trình

            if (written == contentLength) {
                Serial.println("Firmware successfully written.");
                if (Update.end()) {
                    return true;
                } else {
                    Serial.printf("Update error: %s\n", Update.errorString());
                }
            } else {
                Serial.printf("Written only %d/%d bytes. Update failed.\n", written, contentLength);
            }
        } else {
            Serial.println("Not enough space for update.");
        }
    } else {
        Serial.printf("Failed to download firmware. HTTP code: %d\n", httpCode);
    }
    http.end();
    return false;
}
