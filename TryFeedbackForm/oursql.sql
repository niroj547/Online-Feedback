CREATE DATABASE feedback_system;

USE feedback_system;

CREATE TABLE feedback (
  id INT AUTO_INCREMENT PRIMARY KEY,
  student_name VARCHAR(100),
  academic_year VARCHAR(10),
  semester VARCHAR(10),
  feedback_date DATE,
  section VARCHAR(10),
  anonymous_mode BOOLEAN,
  course VARCHAR(100),
  lecturer_rating INT,
  tutor_rating INT,
  comment TEXT
);
