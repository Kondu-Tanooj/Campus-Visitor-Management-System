from flask import Flask, render_template, request, jsonify, redirect
import cv2
import os
import face_recognition
import pickle
import subprocess
import base64
import requests 
import numpy as np
from mtcnn import MTCNN
import signal
import multiprocessing

app = Flask(__name__)

# Directory to store training images
TRAINING_DIR = "training-images"
os.makedirs(TRAINING_DIR, exist_ok=True)

# Global variable for recognition process
recognition_process = None

def face_recognize_p1():
    """Function to recognize faces from webcam"""
    with open("encodings.pkl", "rb") as f:
        data = pickle.load(f)

    known_encodings = data["encodings"]
    known_names = data["names"]

    detector = MTCNN()
    video_capture = cv2.VideoCapture(0)
    recognized_people = set()
    recognized_encodings = []

    while True:
        ret, frame = video_capture.read()
        if not ret:
            break

        rgb_frame = cv2.cvtColor(frame, cv2.COLOR_BGR2RGB)
        faces = detector.detect_faces(rgb_frame)
        face_locations = [(f['box'][1], f['box'][0] + f['box'][2], f['box'][1] + f['box'][3], f['box'][0]) for f in faces]
        face_encodings = face_recognition.face_encodings(rgb_frame, face_locations)

        for face_encoding, (top, right, bottom, left) in zip(face_encodings, face_locations):
            matches = face_recognition.compare_faces(known_encodings, face_encoding, tolerance=0.5)
            best_match_index = np.argmin(face_recognition.face_distance(known_encodings, face_encoding)) if matches else -1

            if best_match_index >= 0 and matches[best_match_index]:
                name = known_names[best_match_index]
                send_to_php(name)

                if name not in recognized_people:
                    recognized_people.add(name)
                    recognized_encodings.append(face_encoding)

                cv2.rectangle(frame, (left, top), (right, bottom), (0, 255, 0), 2)
                cv2.putText(frame, name, (left, top - 10), cv2.FONT_HERSHEY_SIMPLEX, 0.8, (0, 255, 0), 2)
            else:
                cv2.rectangle(frame, (left, top), (right, bottom), (0, 0, 255), 2)
                cv2.putText(frame, "Unknown", (left, top - 10), cv2.FONT_HERSHEY_SIMPLEX, 0.8, (0, 0, 255), 2)

        cv2.imshow("Face Recognition", frame)
        if cv2.waitKey(1) & 0xFF == ord('q'):
            break

    video_capture.release()
    cv2.destroyAllWindows()

def send_to_php(name):
    """Send recognized name to PHP script via POST request."""
    url = "http://127.0.0.1/cvms/cam/insert_capture.php"
    data = {"regd_id": name}

    try:
        response = requests.post(url, data=data)
        if response.status_code == 200:
            print(f"✔️ Successfully sent {name} to PHP script.")
        else:
            print(f"❌ Failed to send data. Status code: {response.status_code}")
    except requests.exceptions.RequestException as e:
        print(f"❌ Error sending data: {e}")

@app.route("/")
def home():
    return "Flask Server Running"

@app.route("/register")
def register():
    return render_template("register.html")

@app.route('/capture', methods=['POST'])    
def capture():
    """Capture image from frontend and store it for training"""
    try:
        data = request.json
        if not data:
            return jsonify({"error": "No JSON data received"}), 400

        image_data = data.get("image")
        student_name = data.get("name", "").strip()

        if not image_data or not student_name:
            return jsonify({"error": "Missing image data or student name"}), 400

        image_bytes = base64.b64decode(image_data.split(",")[1])
        frame = cv2.imdecode(np.frombuffer(image_bytes, np.uint8), cv2.IMREAD_COLOR)

        if frame is None:
            return jsonify({"error": "Failed to decode image"}), 400

        student_folder = os.path.join(TRAINING_DIR, student_name)
        os.makedirs(student_folder, exist_ok=True)

        file_path = os.path.join(student_folder, f"{student_name}_{len(os.listdir(student_folder)) + 1}.jpg")
        cv2.imwrite(file_path, frame)

        return jsonify({"message": "Image captured successfully", "path": file_path}), 200
    except Exception as e:
        return jsonify({"error": f"Server error: {str(e)}"}), 500

@app.route("/train", methods=["GET"])
def train_model():
    """Train the model"""
    try:
        process = subprocess.run(["python3", "train.py"], capture_output=True, text=True)
        return '''  <script type="text/javascript">
                        alert("Training completed!");
                        window.location.href = "http://localhost/cvms/app/cam.php";
                    </script>  '''
    except Exception as e:
        return '''  <script type="text/javascript">
                        alert("Error while Training!");
                        window.location.href = "http://localhost/cvms/app/cam.php";
                    </script>  '''

@app.route("/recognize", methods=["GET"])
def recognize():
    """Start face recognition"""
    global recognition_process
    if recognition_process and recognition_process.is_alive():
        return jsonify({"message": "Recognition already running"}), 400
    
    recognition_process = multiprocessing.Process(target=face_recognize_p1)
    recognition_process.start()
    
    return '''
                    <script type="text/javascript">
                    alert('Face recognition started!');
                        window.location.href = "http://localhost/cvms/app/cam.php";
                    </script>
        '''

@app.route("/stop", methods=["GET"])
def stop_recognition():
    """Stop face recognition"""
    global recognition_process
    if recognition_process and recognition_process.is_alive():
        recognition_process.terminate()
        recognition_process.join()
        recognition_process = None
        return '''
                    <script type="text/javascript">
                        window.location.href = "http://localhost/cvms/app/cam.php";
                    </script>
        '''
    
    return '''
                <script type="text/javascript">
                    window.location.href = "http://localhost/cvms/app/cam.php";
                </script>
    '''

if __name__ == "__main__":
    app.run(host="0.0.0.0", port=5001, debug=True)
