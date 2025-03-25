#include <ESP8266WiFi.h>
#include <ESP8266HTTPClient.h>
#include <MFRC522.h>
#include <SPI.h>
#include <Wire.h>
#include <hd44780.h>          // LCD
#include <hd44780ioClass/hd44780_I2Cexp.h>
#include <ArduinoJson.h>

#define RST_PIN   D3     // Reset pin for MFRC522
#define SS_PIN    D4     // Slave Select pin for MFRC522
#define BUZZER_PIN D8    // Buzzer pin

MFRC522 mfrc522(SS_PIN, RST_PIN);
hd44780_I2Cexp lcd;

const char* ssid = "Distro";
const char* password = "001010011100";

String lastUID = "";
unsigned long lastReadTime = 0;

void setup() {
  Serial.begin(9600);
  SPI.begin();
  lcd.begin(16, 2);
  lcd.backlight();
  mfrc522.PCD_Init();
  pinMode(BUZZER_PIN, OUTPUT);
  connectToWiFi();
  display();
}

void loop() {
  if (WiFi.status() != WL_CONNECTED) {
    handleWiFiDisconnection();
  } else if (mfrc522.PICC_IsNewCardPresent() && mfrc522.PICC_ReadCardSerial()) {
    String uid = getCardUID();

    // Prevent duplicate readings within 2 seconds
    if (uid != lastUID || millis() - lastReadTime > 2000) {
      lastUID = uid;
      lastReadTime = millis();
      sendSensorDataToServer(uid);
      display();
      delay(500);
    }
  }
}

void connectToWiFi() {
  WiFi.begin(ssid, password);
  lcd.clear();
  lcd.print("Connecting...");

  int attempt = 0;
  while (WiFi.status() != WL_CONNECTED && attempt < 20) {
    delay(1000);
    Serial.println("Connecting to WiFi...");
    attempt++;
  }

  if (WiFi.status() == WL_CONNECTED) {
    Serial.println("Connected to WiFi");
    lcd.clear();
    lcd.print("Connected To");
    lcd.setCursor(0, 1);
    lcd.print(ssid);
    delay(2000);
  } else {
    Serial.println("WiFi Failed");
    lcd.clear();
    lcd.print("WiFi Failed");
  }
}

void handleWiFiDisconnection() {
  lcd.clear();
  lcd.print("Disconnected");
  lcd.setCursor(0, 1);
  lcd.print("Reconnecting...");
  WiFi.disconnect();
  delay(500);
  connectToWiFi();
}

String getCardUID() {
  String uid = "";
  for (byte i = 0; i < mfrc522.uid.size; i++) {
    uid += String(mfrc522.uid.uidByte[i] < 0x10 ? "0" : "");
    uid += String(mfrc522.uid.uidByte[i], HEX);
  }
  uid.toUpperCase();
  Serial.println("Card UID: " + uid);
  return uid;
}

void sendSensorDataToServer(String uid) {
  HTTPClient http;
  WiFiClient client;

  http.begin(client, "http://192.168.7.228/cvms/dev-api/register.php");
  http.addHeader("Content-Type", "application/x-www-form-urlencoded");
  int httpResponseCode = http.POST("sensor1=" + uid);

  if (httpResponseCode > 0) {
    String response = http.getString();
    Serial.println("Response: " + response);
    // Parse JSON response
    StaticJsonDocument<200> doc;
    DeserializationError error = deserializeJson(doc, response);

    if (error) {
      Serial.print("JSON Parsing Error: ");
      Serial.println(error.c_str());
      return;
    }

    // Extract "id_name" from JSON
    String id_name = doc["id_name"].as<String>();
    
    Serial.println("ID Name: " + id_name);

    // Display Access Granted on LCD
    lcd.clear();
    lcd.print("Access Granted");
    lcd.setCursor(0, 1);
    lcd.print(id_name);
    tone(BUZZER_PIN, 1000, 300);
    delay(2000);
    lcd.clear();
  } else {
    handleHTTPError(httpResponseCode);
  }
  http.end();
}

void display() {
  lcd.clear();
  lcd.setCursor(3, 0);  // Adjust position for proper centering
  lcd.print("MVGR CVMS");
  lcd.setCursor(0, 1);  // Adjust position for proper centering
  lcd.print("Tap to Register");
}

void handleHTTPError(int httpResponseCode) {
  Serial.print("Error on HTTP request, code: ");
  Serial.println(httpResponseCode);
  lcd.clear();
  lcd.print("DB Connection");
  lcd.setCursor(0, 1);
  lcd.print("Failed!");
  tone(BUZZER_PIN, 1000, 300);
  delay(500);
  tone(BUZZER_PIN, 1000, 300);
  delay(1000);
}
