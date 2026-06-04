# Web-Based Exam CBT Application

A Computer Based Test (CBT) application for online exams with complete features for Admin, Teachers, and Students.

## 📋 Technical Specifications

- **Environment**: Laragon 6.0 / XAMPP
- **Web Server**: Apache httpd-2.4.54
- **Database**: MySQL 8.0.30
- **Programming Language**: PHP Native
- **Frontend**: HTML5, CSS3, JavaScript

## 🚀 Application Features

### 1. Multi-Role Login
- ✅ Admin Login
- ✅ Teacher Login  
- ✅ Student Login

### 2. Admin Features
- Statistics dashboard
- User Management (Admin, Teacher, Student)
- Subject Management
- Monitoring all exams and questions

### 3. Teacher Features
- Statistics dashboard
- Question Bank (CRUD)
- **Import Questions from DOCX/TXT** with template format
- Multiple Choice Questions (A, B, C, D, E)
- Essay Questions
- Exam Management
- Student Grading

### 4. Student Features
- Exam dashboard
- Available exam list
- Take online exams
- Exam timer
- View grades

## 📁 Folder Structure

```
exam-cbt/
├── admin/                  # Admin Panel
│   └── dashboard.php
├── assets/
│   ├── css/
│   ├── js/
│   └── uploads/           # Upload folder
├── config/
│   ├── config.php         # Application configuration
│   └── database.sql       # Database script
├── guru/                  # Teacher Panel
│   ├── dashboard.php
│   ├── soal.php
│   └── import-soal.php
├── imports/
│   ├── SoalImporter.php   # Question import parser
│   └── template.txt       # Question format template
├── models/
│   ├── Auth.php           # Authentication model
│   └── Soal.php           # Question model
├── siswa/                 # Student Panel
│   └── dashboard.php
├── views/
│   └── layouts/           # Layout templates
│       ├── main.php
│       ├── admin.php
│       ├── guru.php
│       └── siswa.php
├── login.php              # Login page
└── index.php              # Entry point
```

## 🛠️ Installation

### 1. Environment Preparation

Make sure you have installed:
- Laragon 6.0 or XAMPP
- MySQL 8.0.30

### 2. Database Setup

1. Open phpMyAdmin (http://localhost/phpmyadmin)
2. Create a new database named `exam_cbt`
3. Import file `config/database.sql`
4. Or run the SQL script in `config/database.sql`

### 3. Application Configuration

1. Copy the `exam-cbt` folder into the `www` folder (Laragon) or `htdocs` (XAMPP)
2. Open the file `config/config.php`
3. Adjust configuration if needed:
   ```php
   define('DB_HOST', 'localhost');
   define('DB_USER', 'root');
   define('DB_PASS', '');
   define('DB_NAME', 'exam_cbt');
   define('BASE_URL', 'http://localhost/exam-cbt');
   ```

### 4. Access the Application

Open your browser and access:
```
http://localhost/exam-cbt
```

## 👤 Default Login Credentials

| Role    | Username | Password |
|---------|----------|----------|
| Admin   | admin    | password |
| Teacher | guru1    | password |
| Student | siswa1   | password |

## 📝 Question Import Format

### TXT/DOCX File Format

```
[QUESTION_TYPE:MC]
[SUBJECT:Mathematics]
[WEIGHT:1]
QUESTION: What is the result of 5 + 3?
A. 5
B. 6
C. 7
D. 8
E. 9
ANSWER: D

[QUESTION_TYPE:ESSAY]
[SUBJECT:Indonesian Language]
[WEIGHT:2]
QUESTION: Explain the definition of free verse poetry!
ANSWER: Free verse poetry is poetry that is not bound by rules such as rhyme, rhythm, and number of lines.
```

### Format Description:
- `QUESTION_TYPE`: MC for Multiple Choice, ESSAY for Essay
- `SUBJECT`: Subject name
- `WEIGHT`: Score/weight of the question (default: 1)
- For MC, options A through E are required
- `ANSWER`: Correct answer (A/B/C/D/E for MC)
- Separate each question with a blank line

Complete template available at: `imports/template.txt`

## 🎯 Supported Question Types

### 1. Multiple Choice (A, B, C, D, E)
- Automatic grading system
- Single correct answer

### 2. Essay
- Manual grading by teacher
- Adjustable score weight

## 🔒 Security

- Password hashed using bcrypt
- Session management
- SQL Injection protection (PDO Prepared Statements)
- XSS Protection (htmlspecialchars)
- File upload validation

## 📄 License

This application is created for educational purposes and can be further developed according to needs.

---

**Made with ❤️ for Indonesian Education**
