/**
   ESP8266-OTA-PHP.ino

   Created on: 04.04.2016

*/

#include <Arduino.h>
#include <ESP8266WiFi.h>
#include <ESP8266WiFiMulti.h>
#include <ESP8266HTTPClient.h>
#include <ESP8266httpUpdate.h>

#define USE_SERIAL Serial
#define VERSION "ESP8266-OTA-PHP-0.0.1"

#define WIFI_SSID "EUReKA"
#define WIFI_PWD "Alaska645!"

#define OTA_HOST "10.1.1.4"
#define OTA_PORT 80
#define OTA_PATH "/esp/update.php"

const int updatePin = 5;

ESP8266WiFiMulti WiFiMulti;

void setup() {
  USE_SERIAL.begin(115200);
  USE_SERIAL.setDebugOutput(false);
  USE_SERIAL.println();
  USE_SERIAL.println();
  USE_SERIAL.println();

  for (uint8_t t = 4; t > 0; t--) {
    USE_SERIAL.printf("[SETUP] WAIT %d...\n", t);
    USE_SERIAL.flush();
    delay(1000);
  }

  pinMode(updatePin, INPUT_PULLUP);
  WiFiMulti.addAP(WIFI_SSID, WIFI_PWD);

  // put your setup code here, to run once:
  
}

void loop() {
  if (digitalRead(updatePin) == LOW) {
    if ((WiFiMulti.run() == WL_CONNECTED)) {
      t_httpUpdate_return ret = ESPhttpUpdate.update(OTA_HOST, OTA_PORT, OTA_PATH, VERSION);
  
      switch (ret) {
        case HTTP_UPDATE_FAILED:
          USE_SERIAL.printf("HTTP_UPDATE_FAILD Error (%d): %s", ESPhttpUpdate.getLastError(), ESPhttpUpdate.getLastErrorString().c_str());
          break;
  
        case HTTP_UPDATE_NO_UPDATES:
          USE_SERIAL.println("HTTP_UPDATE_NO_UPDATES");
          break;
  
        case HTTP_UPDATE_OK:
          USE_SERIAL.println("HTTP_UPDATE_OK");
          break;
      }
    }
  }

  // put your main code here, to run repeatedly:
  
}

