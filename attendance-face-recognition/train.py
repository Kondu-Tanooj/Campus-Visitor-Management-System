import face_recognition
import os
import pickle
from PIL import Image

# Folder containing student folders
training_folder = "training-images"

known_encodings = []
known_names = []

# Iterate through each student's folder
for student_name in os.listdir(training_folder):
    student_path = os.path.join(training_folder, student_name)

    if os.path.isdir(student_path):  # Ensure it's a folder
        for file in os.listdir(student_path):
            image_path = os.path.join(student_path, file)

            # Resize image
            image = Image.open(image_path)
            image = image.resize((300, 300))  
            image.save(image_path)  

            # Extract face encodings
            image_array = face_recognition.load_image_file(image_path)
            face_encodings = face_recognition.face_encodings(image_array)

            if len(face_encodings) > 0:
                known_encodings.append(face_encodings[0])
                known_names.append(student_name)  # Store the folder (student) name
            else:
                print(f"Warning: No face found in {file}, skipping.")

# Save encodings
encoding_data = {"encodings": known_encodings, "names": known_names}
with open("encodings.pkl", "wb") as f:
    pickle.dump(encoding_data, f)

print("Training complete! Encodings saved.")
