#include <WiFi.h>
#include <HTTPClient.h>
#include <Update.h>
#include <ArduinoJson.h>

const char* ssid = "quynh";      
const char* password = "88888888";
// const char* serverURL = "https://vinhcaodatabase.com/ota/firmware.php";
const char* serverURL = "http://192.168.1.6/ota/firmware.php";
const String currentVersion = "1.0"; 

void setup() {
  Serial.begin(115200);
  WiFi.begin(ssid, password);

  Serial.println("Connecting to WiFi...");
  while (WiFi.status() != WL_CONNECTED) {
    delay(1000);
    Serial.print(".");
  }
  Serial.println("\nConnected to WiFi");

  checkForUpdates();
}

void loop() {
  delay(60000); 
}

void checkForUpdates() {
  if (WiFi.status() == WL_CONNECTED) {
    HTTPClient http;
    String url = String(serverURL) + "?version=" + currentVersion;
    http.begin(url);

    int httpCode = http.GET();
    if (httpCode == HTTP_CODE_OK) {
      String payload = http.getString();
      Serial.println("Server response: " + payload);

      // Phân tích JSON từ server
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

bool downloadFirmware(const String& url) {
  HTTPClient http;
  http.begin(url);

  int httpCode = http.GET();
  if (httpCode == HTTP_CODE_OK) {
    int contentLength = http.getSize();
    WiFiClient* stream = http.getStreamPtr();

    if (Update.begin(contentLength)) {
      size_t written = 0;
      uint8_t buffer[128];
      while (http.connected() && (written < contentLength)) {
        size_t sizeAvailable = stream->available();
        if (sizeAvailable) {
          size_t chunkSize = stream->readBytes(buffer, min(sizeAvailable, sizeof(buffer)));
          size_t bytesWritten = Update.write(buffer, chunkSize);

          written += bytesWritten;

  
          Serial.printf("Downloaded: %d%%\n", (written * 100) / contentLength);
        }
        delay(1);
      }

      if (written == contentLength) {
        Serial.println("Firmware successfully downloaded.");
        if (Update.end()) {
          Serial.println("Installation complete.");
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
