#include <ESP8266WiFi.h>
#include <ESP8266HTTPClient.h>
#include <MFRC522.h>
#include <SPI.h>
#include <Wire.h>
#include <hd44780.h>          // main hd44780 header
#include <hd44780ioClass/hd44780_I2Cexp.h>

#define RST_PIN   D3     // Reset pin for MFRC522
#define SS_PIN    D4     // Slave Select pin for MFRC522
#define BUZZER_PIN D8    // Buzzer pin

MFRC522 mfrc522(SS_PIN, RST_PIN); // Create MFRC522 instance
hd44780_I2Cexp lcd;

const char* ssid = "Distro";
const char* password = "001010011100";
const char* devname = "dev_2"; // Device name

void setup() {
  Serial.begin(9600);
  SPI.begin();
  lcd.begin(20, 4);  // Initialize 16x2 LCD
  lcd.backlight();
  mfrc522.PCD_Init();
  connectToWiFi();
  display();
}

void loop() {
  if (WiFi.status() == WL_CONNECTED) {
    if (mfrc522.PICC_IsNewCardPresent() && mfrc522.PICC_ReadCardSerial()) {
      String uid = getCardUID();
      sendSensorDataToServer(uid);
      delay(1000); // Delay to avoid multiple readings
      display();
    }
  } else {
    handleWiFiDisconnection();
  }
}

void connectToWiFi() {
  WiFi.begin(ssid, password);
  while (WiFi.status() != WL_CONNECTED) {
    delay(1000);
    Serial.println("Connecting to WiFi...");
    lcd.print("Connecting....");
  }
  Serial.println("Connected to WiFi");
  lcd.clear();
  lcd.setCursor(0, 0);
  lcd.print("Connected To");
  lcd.setCursor(0, 1);
  lcd.print(ssid);
  delay(3000);
  lcd.clear();
}

void display() {
  lcd.clear();
  lcd.setCursor(5, 0);  // Adjust position for proper centering
  lcd.print("MVGR CVMS");
  lcd.setCursor(5, 2);  // Adjust position for proper centering
  lcd.print("Tap Here");
}

void handleWiFiDisconnection() {
  lcd.clear();
  lcd.setCursor(0, 0);
  lcd.print("Disconnected");
  lcd.setCursor(0, 1);
  lcd.print("Searching..");
  delay(500);
  WiFi.begin(ssid, password);
  delay(3000);
}

String getCardUID() {
  String uid = "";
  for (byte i = 0; i < mfrc522.uid.size; i++) {
    uid.concat(String(mfrc522.uid.uidByte[i] < 0x10 ? "0" : ""));
    uid.concat(String(mfrc522.uid.uidByte[i], HEX));
  }
  uid.toUpperCase();
  Serial.println("Card UID: " + uid);
  return uid;
}

void sendSensorDataToServer(String uid) {
  HTTPClient http;
  WiFiClient client;
  String url = "http://192.168.7.228/cvms/dev-api/dev_2.php";
  String postData = "sensor1=" + uid + "&devname=" + devname;

  http.begin(client, url);
  http.addHeader("Content-Type", "application/x-www-form-urlencoded");
  int httpResponseCode = http.POST(postData);

  if (httpResponseCode > 0) {
    String response = http.getString();
    Serial.println("Server Response: " + response);
    handleServerResponse(response);
  } else {
    handleHTTPError(httpResponseCode);
  }
  http.end();
}

void handleServerResponse(String response) {
  lcd.clear();
  if (response == "CLASS") {
    lcd.print("CLASS");
    tone(BUZZER_PIN, 1000, 300);
    delay(500);
    tone(BUZZER_PIN, 1000, 300);
  } else if (response == "Invalid ID") {
    lcd.print("Invalid ID");
    tone(BUZZER_PIN, 1000, 1000);
  } else if (response == "NO CLASS") {
    lcd.print("NO CLASS");
    tone(BUZZER_PIN, 1000, 300);
  } else {
    lcd.setCursor(1, 1); 
    lcd.print(response); // Display other responses (e.g., "Invalid ID", "User Not Allowed")
    tone(BUZZER_PIN, 1000, 1000); // Long beep for errors
  }
  delay(2000);
  noTone(BUZZER_PIN);
}

void handleHTTPError(int httpResponseCode) {
  Serial.print("Error on HTTP request, code: ");
  Serial.println(httpResponseCode);
  lcd.clear();
  lcd.setCursor(0, 0);
  lcd.print("Connection lost");
  lcd.setCursor(0, 1);
  lcd.print("with Database");
  tone(BUZZER_PIN, 1000, 300);
  delay(500);
  tone(BUZZER_PIN, 1000, 300);
  delay(1000);
  noTone(BUZZER_PIN);
}