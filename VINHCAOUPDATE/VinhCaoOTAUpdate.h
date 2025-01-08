#ifndef OTA_UPDATE_H
#define OTA_UPDATE_H

#include <WiFi.h>
#include <HTTPClient.h>
#include <Update.h>
#include <ArduinoJson.h>

class OTAUpdate {
public:
    OTAUpdate(const char* serverURL, const String& currentVersion);
    void checkForUpdates();

private:
    const char* _serverURL;
    String _currentVersion;

    bool downloadFirmware(const String& url);
};

#endif
