# 🏥 Online Medical System — MediBook

An interactive, secure, and user-friendly **Online Medical System** built with **HTML, CSS, JavaScript, PHP, and MySQL**.  
This project streamlines healthcare management by enabling **patients**, **doctors**, and **administrators** to efficiently manage appointments, medical records, and communications through a centralized web platform.

---

## 📖 Table of Contents
1. [Introduction](#introduction)
2. [Features](#features)
3. [Technologies Used](#technologies-used)
4. [System Design](#system-design)
5. [Database Design](#database-design)
6. [Installation & Setup](#installation--setup)
7. [Testing & Validation](#testing--validation)
8. [Challenges Faced](#challenges-faced)
9. [Future Improvements](#future-improvements)
10. [Author](#author)
11. [License](#license)

---

## 🩺 Introduction

The **Online Medical System (MediBook)** provides a digital solution for common inefficiencies in traditional healthcare systems.  
It reduces patient waiting times, improves appointment management, centralizes medical records, and enhances communication between patients and healthcare providers.

### 🎯 Purpose
To provide a **streamlined healthcare experience** by:
- Automating appointment scheduling
- Centralizing patient and doctor records
- Improving communication between patients, doctors, and clinic administrators

---

## 🌟 Features

### 👨‍⚕️ For Patients
- Register and log in securely  
- Book and manage appointments online  
- View personal medical history and prescriptions  
- Access a personalized patient dashboard  

### 🩺 For Doctors
- Manage their appointment schedule  
- View and update patient medical records  
- Access doctor-specific dashboards  

### 🧑‍💼 For Administrators
- Manage all system users (patients and doctors)  
- Oversee appointments and medical records  
- View and manage system messages and reports  

---

## 💻 Technologies Used

| Layer | Technology |
|-------|-------------|
| **Frontend** | HTML5, CSS3, JavaScript, Font Awesome |
| **Backend** | PHP (Form handling, Authentication, CRUD operations) |
| **Database** | MySQL |
| **Server Environment** | XAMPP (Apache, MySQL, PHP) |
| **External Libraries** | Dompdf (PDF generation), Font Awesome (Icons) |

---

## 🧭 System Design

### 🌐 Key Pages
- `index.php` — Homepage  
- `about.php`, `contact.php`, `faq.php` — Informational pages  
- `login.php`, `register.php` — Authentication  
- `patient_dashboard.php`, `doctor_dashboard.php`, `admin_dashboard.php` — Role-based dashboards  
- `book_appointment.php`, `view_appointments.php` — Appointment management  
- `medical_records.php`, `download_medical_record.php` — Medical record access  

### 🧩 Navigation Flow
- Public pages are accessible to all visitors.  
- Upon login, users are redirected to their respective dashboards based on their roles.  
- Each dashboard provides specific functionalities relevant to the user type.

---

## 🗃️ Database Design

### 🧱 Core Tables
| Table | Description |
|--------|--------------|
| **users** | Stores user credentials and roles (`patient`, `doctor`, `admin`) |
| **patients** | Patient personal information |
| **doctors** | Doctor details including specialization and schedule |
| **appointments** | Booking and scheduling data |
| **medical_records** | Patient diagnosis, prescriptions, and notes |
| **specializations** | List of medical specializations |
| **contact_messages** | Messages sent from the contact form |
| **doctor_schedules** | Availability and time slots of doctors |

### 🔗 Relationships
- `patients.user_id → users.user_id`  
- `doctors.user_id → users.user_id`  
- `appointments.patient_id → patients.patient_id`  
- `appointments.doctor_id → doctors.doctor_id`  
- `medical_records.patient_id → patients.patient_id`  
- `medical_records.doctor_id → doctors.doctor_id`  

---

## ⚙️ Installation & Setup

### Prerequisites
- Install [XAMPP](https://www.apachefriends.org/download.html)
- Ensure Apache and MySQL are running

### Steps
1. Clone the repository:
   ```bash
   git clone https://github.com/Aline-eng/Online-Medical-System.git

### 🧪 Testing & Validation

Manual Testing: Validated all user interactions and workflows

Cross-Browser: Works on Chrome, Edge, and Brave

Responsive Design: Fully tested on mobile, tablet, and desktop

Form Validation: Both client-side (JavaScript) and server-side (PHP)

Role-based Access: Users only access permitted features

### 🚧 Challenges Faced

MySQL Server Crashes — Solved by resolving port conflicts and reinstalling XAMPP

CSS Conflicts — Fixed through modular and scoped CSS structure

PDF Export Issue — Implemented Dompdf library for structured, styled PDF generation

### 🚀 Future Improvements

Real-time appointment scheduling with availability checks

Telemedicine integration (video consultations)

Online payment support for consultations

Advanced notification system (email/SMS alerts)

Multi-Factor Authentication (MFA)

Enhanced search and filtering for doctors and records

### 👩‍💻 Author

Nzikwinkunda Aline
Course: Web Design
Institution: [Adventist University of Central Africa, Rwanda]

### 📜 License

This project is for educational purposes. You may modify and adapt it for learning or research use.
