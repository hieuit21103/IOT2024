#include <ESP8266WiFi.h>
#include <ESP8266HTTPClient.h>
#include <EEPROM.h>
#include <map>
#include <ESP8266WebServer.h>
#include <ESP8266mDNS.h>
#include <SoftwareSerial.h>
#include <ArduinoJson.h>
#include <Wire.h>
#include <RTClib.h>

#define EEPROM_SIZE 96
#define SDA_PIN D5
#define SCL_PIN D6

std::map<String,String> dictionary;
bool isScanned = false;
int mode = 1;
int speed = 127;
String timeArray[10];
ESP8266WebServer server(80);
SoftwareSerial arduino(4,5);
String url = "http://192.168.220.251/iot";
RTC_DS3231 rtc;

void handleRoot(){
  server.send(200,"text/plain","SERVER IS RUNNING");
}
void handleJson() {
    server.sendHeader("Access-Control-Allow-Origin", "*");
    if (server.hasArg("type")) {
        String type = server.arg("type");  

        if (type == "mt") {
            StaticJsonDocument<200> doc;
            arduino.println("mt");
            delay(1000);
            if (arduino.available() > 0) {
                String data = arduino.readStringUntil('\n');
                data.trim();
                if (data.startsWith("moisture:")) {
                    int moisture = data.substring(data.indexOf("moisture:") + 9, data.indexOf(" ")).toInt();
                    int temperature = data.substring(data.indexOf("temperature:") + 12).toInt();
                    doc["moisture"] = moisture;
                    doc["temperature"] = temperature;
                }
                String jsonData;
                serializeJson(doc, jsonData);
                server.send(200, "application/json", jsonData);    
            }
        } 
        else if (type == "mode") {
            StaticJsonDocument<200> doc;
            if (mode == 0) {
                doc["mode"] = "auto";
            } else {
                doc["mode"] = "manual";
            }

            String jsonData;
            serializeJson(doc, jsonData);
            server.send(200, "application/json", jsonData);
        } 
        else if (type == "station") {
            StaticJsonDocument<200> doc;
            arduino.println("station");
            delay(1000);
            if (arduino.available() > 0) {
                String data = arduino.readStringUntil('\n');
                data.trim();
                if (data.startsWith("pump:")) {
                    doc["pump"] = data.substring(5);
                }
                
                String jsonData;
                serializeJson(doc, jsonData);
                server.send(200, "application/json", jsonData);
            }
        else {
            server.send(400, "text/plain", "Invalid type argument");
        }
    } 
    else {
        server.send(400, "text/plain", "Type argument missing");
    }
}
}

void handleOn() {
  if(server.hasArg("speed")){
    int motorSpeed = server.arg("speed").toInt();
    arduino.printf("on %d \n",motorSpeed);
    Serial.printf("on %d \n",motorSpeed);
    log("on");
    server.send(200);
  }
}
void handleOff() {
  arduino.println("off");
  Serial.println("off");
  log("off");
  server.send(200);
}
void handleAuto() {
  if (server.hasArg("preset")) {
    String preset = server.arg("preset");
    String surl = url+"/fetch_data.php?preset=" + preset;
    WiFiClient client;
    HTTPClient http;
    
    http.begin(client,surl);
    http.setTimeout(10000);
    int httpResponseCode = http.GET();
    
    if (httpResponseCode > 0) {
      String response = http.getString();
      StaticJsonDocument<1024> doc;
      DeserializationError error = deserializeJson(doc, response);
      
      if (!error) {
        JsonArray array = doc.as<JsonArray>();
        for (JsonObject obj : array) {
          speed = obj["speed"];
          String timeString = obj["time"];
          mode = 0;
          int index = 0;
          int startIndex = 0;
          while ((startIndex = timeString.indexOf(' ', startIndex)) != -1) {
            timeArray[index++] = timeString.substring(0, startIndex);
            timeString = timeString.substring(startIndex + 1);
          }
          if (timeString.length() > 0) {
            timeArray[index++] = timeString;
          }
          break;
        }
        
        server.send(200);
      } else {
        server.send(500, "text/plain", "Failed to parse JSON");
      }
    } else {
      server.send(500, "text/plain", "Error on HTTP request: " + String(httpResponseCode));
    }
    
    http.end();
  } else {
    server.send(400, "text/plain", "Bad Request: Missing 'preset' parameter");
  }
}
void handleManual() {
  server.sendHeader("Access-Control-Allow-Origin", "*");
  mode = 1;
  server.send(200);
}

void handleLogs() {
  server.sendHeader("Access-Control-Allow-Origin", "*");
  server.send(200, "text/plain", "Under development");
}

void save(String ssid, String password) {
  for (int i = 0; i < ssid.length(); i++) {
    EEPROM.write(i, ssid[i]);
  }
  EEPROM.write(ssid.length(), '\0');
  for (int i = 0; i < password.length(); i++) {
    EEPROM.write(32 + i, password[i]);
  }
  EEPROM.write(32 + password.length(), '\0');
  EEPROM.commit();
  Serial.println("SAVED");
}

void scan() {
  Serial.println("Scanning...");
  int c = WiFi.scanNetworks();
  Serial.printf("Found %d :\n", c);
  dictionary.clear(); 

  for (int i = 0; i < c; i++) {
    String ssid = WiFi.SSID(i);
    dictionary[String(i + 1)] = ssid; 
    Serial.printf("%d: %s\n", i + 1, ssid.c_str());
  }
  WiFi.scanDelete();
}

void connect(String ssid, String password) {
  Serial.printf("Connecting to %s...\n", ssid.c_str());
  WiFi.begin(ssid.c_str(), password.c_str());
  int timeout = 10;
  int sec = 0;
  while (WiFi.status() != WL_CONNECTED && sec < timeout) {
    delay(1000);
    Serial.print(".");
    sec++;
  }
  if (WiFi.status() == WL_CONNECTED) {
    Serial.println("\nConnected!");
    Serial.print("Local IP Address: ");
    Serial.println(WiFi.localIP());
    save(ssid,password);
  } else {
    Serial.println("\nConnection timeout.");
  }
}



bool autoConnect() {
  char ssid[32];
  char password[32];
  
  EEPROM.begin(EEPROM_SIZE);
  EEPROM.get(0, ssid);
  EEPROM.get(32, password);

  if (strlen(ssid) > 0) {
    Serial.printf("Trying to auto-connect to %s...\n", ssid);
    WiFi.begin(ssid, password);

    int timeout = 10;
    int sec = 0;

    while (WiFi.status() != WL_CONNECTED && sec < timeout) {
      delay(1000);
      Serial.print(".");
      sec++;
    }

    if (WiFi.status() == WL_CONNECTED) {
      Serial.println("\nAuto-connected!");
      Serial.print("IP Address: ");
      Serial.println(WiFi.localIP());
      return true;
    } else {
      Serial.println("\nAuto-connect failed.");
      return false;
    }
  } else {
    Serial.println("No saved WiFi credentials found.");
    return false;
  }
}

bool check(int hrs,int mins) {
  for (int i = 0; i < sizeof(timeArray) / sizeof(timeArray[0]); i++) {
    if (timeArray[i].length() > 0) {
      if (hrs == timeArray[i].toInt() && mins == 0) {
        return true;
      }
    }
  }
  return false;
}

void log(String station){
  DateTime now = rtc.now();
  String timestamp = String(now.year()) + "-" + String(now.month()) + "-" + String(now.day()) + "%20" + String(now.hour()) + ":" + String(now.minute()) + ":" + String(now.second());
  // String timestamp = "test";
  String modeString = "";
  if(mode == 0){
    modeString = "auto";
  }else if(mode == 1){
    modeString = "manual";
  }
  if (WiFi.status() == WL_CONNECTED) {
    WiFiClient client;
    HTTPClient http;
    String surl = url+"/log.php?timestamp=" + timestamp + "&mode=" + modeString + "&station=" + station + "&speed=" + String(speed);
    Serial.println(surl);
    
    http.begin(client,surl);
    int httpResponseCode = http.GET();
    
    if (httpResponseCode > 0) {
      String response = http.getString();
      Serial.println("HTTP Response code: " + String(httpResponseCode));
    } else {
      Serial.println("Error on HTTP request: " + String(httpResponseCode));
    }
    http.end();
  } else {
    Serial.println("WiFi not connected");
  }
}

void setup() {
  Serial.begin(9600);
  arduino.begin(9600);
  Wire.begin(SDA_PIN, SCL_PIN);
  if (!rtc.begin()) {
    Serial.println("Couldn't find RTC");
  }
  if (rtc.lostPower()) {
    Serial.println("RTC lost power, setting the time!");
    rtc.adjust(DateTime(F(__DATE__), F(__TIME__)));
  }
  EEPROM.begin(EEPROM_SIZE);
  WiFi.mode(WIFI_STA);
  WiFi.disconnect();
  if(!autoConnect()){
    while(WiFi.status() != WL_CONNECTED){
    if (isScanned) {
      Serial.println("Choose your network index: ");
      while (!Serial.available()) {}
      String index = Serial.readStringUntil('\n');
      index.trim();
  
      if (dictionary.find(index) != dictionary.end()) {
        Serial.println("Enter the password: ");
        while (!Serial.available()) {}
        String pwd = Serial.readStringUntil('\n');
        pwd.trim();
  
        connect(dictionary[index], pwd); 
      } else {
        Serial.println("Invalid network index.");
      }
    } else {
      scan();
      isScanned = true;
    }
  }
  }
  if(WiFi.status() == WL_CONNECTED){
    server.on("/",HTTP_GET,handleRoot);
    server.on("/on",HTTP_GET,handleOn);
    server.on("/off",HTTP_GET,handleOff);
    server.on("/auto",HTTP_GET,handleAuto);
    server.on("/manual",HTTP_GET,handleManual);
    server.on("/logs",HTTP_GET,handleLogs);
    server.on("/json",HTTP_GET,handleJson);
    server.begin();
    if (MDNS.begin("esp8266")) {
      MDNS.addService("http", "tcp", 80);
      Serial.println("MDNS responder started");
    }
  }
}

void loop() {
  server.handleClient();
  MDNS.update();
  DateTime now = rtc.now();
  if(mode == 0){
    int currentHours = now.hour();
    int currentMins = now.minute();
    // int currentHours = 6;
    // int currentMins = 0;
    if(check(currentHours,currentMins)){
      arduino.printf("on %d \n",speed);
      log("on");
    }
    arduino.println("mt");
    delay(1000);
    if (arduino.available() > 0) {
      String data = arduino.readStringUntil('\n');
      data.trim();
      if (data.startsWith("moisture:")) {
        int moisture = data.substring(data.indexOf("moisture:") + 9, data.indexOf(" ")).toInt();
        if(moisture > 50){
          arduino.println("off");
          log("off");
        }
      }
    }
    delay(60000);
  }
}
