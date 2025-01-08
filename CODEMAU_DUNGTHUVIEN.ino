#include <WiFi.h>
#include "VinhCaoOTAUpdate.h"

const char* ssid = "Duy Binh";
const char* password = "12346789";
const char* serverURL = "https://vinhcaodatabase.com/ota/firmware.php";
const String currentVersion = "1.0";

OTAUpdate ota(serverURL, currentVersion);

void setup() {
    Serial.begin(115200);
    WiFi.begin(ssid, password);

    Serial.println("Connecting to WiFi...");
    while (WiFi.status() != WL_CONNECTED) {
        delay(1000);
        Serial.print(".");
    }
    Serial.println("\nConnected to WiFi");

    ota.checkForUpdates();
}

void loop() {
    delay(60000); // Kiểm tra cập nhật mỗi phút
    ota.checkForUpdates();
}
