-- Demo Data for Online Medical System
-- Run this after importing the main database schema

USE online_medical_system;

-- Demo Users (passwords are all 'demo123')
INSERT INTO users (username, email, password, full_name, role, is_active) VALUES
('demo_admin', 'admin@demo.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Demo Administrator', 'admin', 1),
('demo_doctor1', 'doctor1@demo.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Dr. Sarah Johnson', 'doctor', 1),
('demo_doctor2', 'doctor2@demo.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Dr. Michael Chen', 'doctor', 1),
('demo_doctor3', 'doctor3@demo.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Dr. Amina Patel', 'doctor', 1),
('demo_patient1', 'patient1@demo.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'John Smith', 'patient', 1),
('demo_patient2', 'patient2@demo.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Emma Wilson', 'patient', 1),
('demo_patient3', 'patient3@demo.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'David Brown', 'patient', 1);

-- Demo Doctors
INSERT INTO doctors (user_id, specialization_id, license_number, phone, experience_years, consultation_fee, image_url, bio) VALUES
((SELECT user_id FROM users WHERE username = 'demo_doctor1'), 1, 'MD001234', '+1-555-0101', 8, 150.00, 'assets/images/team/sarah-johnson.jpeg', 'Experienced general practitioner with focus on preventive care and family medicine.'),
((SELECT user_id FROM users WHERE username = 'demo_doctor2'), 2, 'MD005678', '+1-555-0102', 12, 200.00, 'assets/images/team/micheal-chen.webp', 'Board-certified cardiologist specializing in interventional cardiology and heart disease prevention.'),
((SELECT user_id FROM users WHERE username = 'demo_doctor3'), 3, 'MD009012', '+1-555-0103', 6, 175.00, 'assets/images/team/amina-patel.jpg', 'Dermatologist with expertise in medical and cosmetic dermatology, skin cancer screening.');

-- Demo Patients
INSERT INTO patients (user_id, phone, address, date_of_birth, gender, emergency_contact, emergency_phone) VALUES
((SELECT user_id FROM users WHERE username = 'demo_patient1'), '+1-555-1001', '123 Main St, Anytown, ST 12345', '1985-03-15', 'male', 'Jane Smith (Wife)', '+1-555-1002'),
((SELECT user_id FROM users WHERE username = 'demo_patient2'), '+1-555-1003', '456 Oak Ave, Somewhere, ST 67890', '1992-07-22', 'female', 'Robert Wilson (Husband)', '+1-555-1004'),
((SELECT user_id FROM users WHERE username = 'demo_patient3'), '+1-555-1005', '789 Pine Rd, Elsewhere, ST 54321', '1978-11-08', 'male', 'Sarah Brown (Sister)', '+1-555-1006');

-- Demo Doctor Schedules
INSERT INTO doctor_schedules (doctor_id, day_of_week, start_time, end_time, is_available) VALUES
-- Dr. Sarah Johnson (General Medicine)
(1, 'Monday', '09:00:00', '17:00:00', 1),
(1, 'Tuesday', '09:00:00', '17:00:00', 1),
(1, 'Wednesday', '09:00:00', '17:00:00', 1),
(1, 'Thursday', '09:00:00', '17:00:00', 1),
(1, 'Friday', '09:00:00', '15:00:00', 1),

-- Dr. Michael Chen (Cardiology)
(2, 'Monday', '08:00:00', '16:00:00', 1),
(2, 'Tuesday', '08:00:00', '16:00:00', 1),
(2, 'Wednesday', '08:00:00', '16:00:00', 1),
(2, 'Thursday', '08:00:00', '16:00:00', 1),
(2, 'Friday', '08:00:00', '14:00:00', 1),

-- Dr. Amina Patel (Dermatology)
(3, 'Tuesday', '10:00:00', '18:00:00', 1),
(3, 'Wednesday', '10:00:00', '18:00:00', 1),
(3, 'Thursday', '10:00:00', '18:00:00', 1),
(3, 'Friday', '10:00:00', '18:00:00', 1),
(3, 'Saturday', '09:00:00', '13:00:00', 1);

-- Demo Appointments
INSERT INTO appointments (patient_id, doctor_id, appointment_date, appointment_time, status, reason, notes) VALUES
(1, 1, '2024-01-15', '10:00:00', 'completed', 'Annual checkup', 'Patient in good health, recommended annual blood work'),
(2, 2, '2024-01-16', '14:30:00', 'completed', 'Chest pain consultation', 'EKG normal, recommended stress test'),
(3, 3, '2024-01-17', '11:15:00', 'completed', 'Skin rash examination', 'Prescribed topical treatment'),
(1, 2, '2024-01-20', '09:30:00', 'scheduled', 'Follow-up cardiology consultation', NULL),
(2, 1, '2024-01-22', '15:00:00', 'scheduled', 'Flu symptoms', NULL);

-- Demo Medical Records
INSERT INTO medical_records (patient_id, doctor_id, appointment_id, diagnosis, prescription, notes, record_date) VALUES
(1, 1, 1, 'Annual Physical Examination - Normal', 'Multivitamin daily, Continue current exercise routine', 'Patient reports feeling well. Vital signs normal. Recommended annual blood work and mammogram.', '2024-01-15'),
(2, 2, 2, 'Atypical Chest Pain - Non-cardiac', 'Ibuprofen 400mg as needed for pain', 'EKG shows normal sinus rhythm. No signs of cardiac issues. Likely musculoskeletal pain.', '2024-01-16'),
(3, 3, 3, 'Contact Dermatitis', 'Hydrocortisone cream 1% twice daily for 7 days', 'Localized rash on forearm consistent with contact dermatitis. Advised to avoid known allergens.', '2024-01-17');

-- Demo Contact Messages
INSERT INTO contact_messages (name, email, subject, message, is_read) VALUES
('Alice Johnson', 'alice@example.com', 'Appointment Inquiry', 'Hello, I would like to know about availability for a dermatology consultation next week.', 0),
('Bob Martinez', 'bob@example.com', 'Insurance Question', 'Do you accept Blue Cross Blue Shield insurance for cardiology services?', 1),
('Carol Davis', 'carol@example.com', 'General Inquiry', 'What are your operating hours and do you offer weekend appointments?', 0);

-- Update appointment IDs in medical records (in case of auto-increment differences)
UPDATE medical_records mr 
JOIN appointments a ON mr.patient_id = a.patient_id AND mr.doctor_id = a.doctor_id AND mr.record_date = a.appointment_date 
SET mr.appointment_id = a.appointment_id 
WHERE mr.appointment_id IS NOT NULL;