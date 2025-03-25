# Campus-Visitor-Management-System : A Local Positioning System.
This a Campus Visitor Management system , in this we use totally of 3 domains are : 1. Internet of Things(IoT), 2.Web application (PHP), 3.Machine Learning (for Face Detection- 2 factor Authentication).

In this project , mainly we have 3 device , 
1. Register --> for registering the Rf Id cards.
2. Main --> (This device can we powered by directly plug into power source) It will be present at entries , for this device we had included 2 Factor Authentication. As we use CCTV for Facial Recognition. After get captured by the Cam then only ID card can be scanned and do valid entry else say "Face not detected."
3. Handheld --> (This device is a Chargble device, last upto 8 hours and can get charges full in 2 hrs. In this we use 2000mah Li Battery of 3.7v & charge by only under 1.5A charger). This device will be given to security , as when they scan the ID card of any student , it will give that the student is having **class work or not**.

**NOTE:** The Handheld device need to get charged by below 1.5A charger.


**Step-1:**  Lets create Devices.

1. Main Device: Make it according to the circuit. upload code "cvms-dev-main" using arduino ide. 

   ![main_device](https://github.com/user-attachments/assets/b63c396d-ffa6-4854-bafd-bf8fa3e7ff62)

2. Handheld Device: Make it according to the circuit. upload code "cvms-dev-handheld" using arduino ide.

   ![handhold_device](https://github.com/user-attachments/assets/aee6565b-0ef9-41d5-9c20-d833c725f263)

3. Register Device: This circuit is also same , in this we only use LCD_I2C, Nodemcu (ESP8266), RF_ID Scanner (MFRC522). upload code "cvms-dev-register" using arduino ide.

4. **NOTE:** Make sure to update the ssid, password & ipaddress or path of the server.


   
**Step-2:**  Host the Web Application on serve, lets use xampp server.

1. Setup xampp server in your system.

2. Start Apache & Mysql Servers.

3. past the 'cvms' folder in htdocs in xampp.

4. create a database in Mysql (for user interface recommended phpmyadmin).

5. create a user on previleges on that database or else use root (make sure to update password for root).

6. import the cvms_db.sql file in the created database.

7. go to cvms folder and u can find **'config.php'** in every folder and update the username and passowrd there.

8. after this once check the web site.


**Step-3:** Flask Server for Facial recognition.

1. just install all the requirements given in the file.

2. start the server "main.py"

3. "train.py" is used to train the images which are captured.
