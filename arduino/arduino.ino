#include <DHT.h>
#include <DHT_U.h>
#include <SoftwareSerial.h>
  

const int DHTPIN = A2;      
const int DHTTYPE = DHT11;
const int sensor = A0;
const int ena = 9;
const int in1 = 6;
const int in2 = 7;

int sdata = 0; 
  
String cdata; 
SoftwareSerial nodemcu(5,3);
DHT dht(DHTPIN, DHTTYPE);

// void split(const String &str, char delimiter, String result[], int &count) {
//     int start = 0;
//     int index = 0;
//     count = 0;
    
//     while (index <= str.length()) {
//         if (str[index] == delimiter || index == str.length()) {
//             result[count++] = str.substring(start, index);
//             start = index + 1;
//         }
//         index++;
//     }
// }


void setup()
{
  Serial.begin(9600); 
  nodemcu.begin(9600);
  dht.begin();
  pinMode(sensor, INPUT);
  pinMode(ena,OUTPUT);
  pinMode(in1,OUTPUT);
  pinMode(in2,OUTPUT);
  digitalWrite(in2,LOW);
  digitalWrite(in1,LOW);
  
}
  
void loop()
{
  if (nodemcu.available() > 0) {
    String req = nodemcu.readStringUntil('\n');
    req.trim();
    Serial.println(req);
    if (req.equals("mt")) {
        int sdata = analogRead(sensor);
        sdata = map(sdata, 1023, 0, 0, 100);
        String moistureData = "moisture:" + String(sdata);
        float t = dht.readTemperature();
        String tempData = "temperature:" + String(t);
        nodemcu.println(moistureData+" "+tempData);
    } 
    else if (req.equals("station")) {
        if (digitalRead(in1) == HIGH) {
            nodemcu.println("pump:on");
        } else {
            nodemcu.println("pump:off");
        }
    } 
    else if (req.startsWith("on")) {
        int spaceIndex = req.indexOf(' ');
        if (spaceIndex > 0) {
            String speedStr = req.substring(spaceIndex + 1);
            int motorSpeed = speedStr.toInt();
            digitalWrite(in1, HIGH);
            analogWrite(ena, motorSpeed);
        }
    } 
    else if (req.equals("off")) {
        digitalWrite(in1, LOW);
        analogWrite(ena, 0);
    }
    delay(1000);
  }
}
