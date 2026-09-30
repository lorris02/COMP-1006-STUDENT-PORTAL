# Student Portal

Student Portal is a PHP application for managing student records. It lets users add, view, edit, and delete students, search by name or ID, and track grades through a simple dashboard.

## Features

- Add, edit, and delete students, with a confirmation step before deletion
- Search by name or ID, sort records, and browse ten records per page
- Student count, average grade, and passing-student summaries
- Responsive interface, labelled forms, keyboard focus states, and accessible errors
- Server-side validation, duplicate ID checks, escaped output, and CSRF tokens
- Public demo with fictional records isolated in each visitor's session
- Private MySQL mode using PDO prepared statements and administrator sign-in

## Run the portfolio demo

Requires PHP 8.3+ with sessions and PDO.

```bash
php -S localhost:8080 -t Php_files/Assignment_1
```

Open http://localhost:8080/view.php. The default mode is demo. Changes last for the browser session and are not shared with other visitors. Restore sample records resets that session only.

## Docker hosting

```bash
docker build -t student-portal .
docker run --rm -p 8080:80 student-portal
```

Deploy the Docker image on a PHP/container-capable host, with container port 80 and HTTPS enabled. Keep APP_MODE=demo for the public portfolio. A static-only host cannot execute these PHP pages.

## Private MySQL mode

Requires the PDO MySQL extension and MySQL 8+. Create a database and limited application user, then import student.sql into a NEW database. For the original coursework database, back it up, inspect its records, and use migrations/001_align_students.sql instead. The application reads student_grade; the legacy grade column is retained by the migration.

Set APP_MODE=database, ADMIN_USERNAME, ADMIN_PASSWORD, DB_HOST, DB_PORT, DB_NAME, DB_USER, and DB_PASSWORD in the server environment. See .env.example for variable names. The app does not automatically load .env files.

Database mode refuses access without configured administrator credentials. Use HTTPS for HTTP Basic authentication. This is a single-administrator school project, not a production college records system.

## Checks

```bash
php tests/run.php
python3 tests/http_test.py http://localhost:8080
```

The PHP tests check validation boundaries, duplicate IDs, and demo CRUD. HTTP tests exercise form submissions, CSRF, escaped output, search, deletion, and session isolation. MySQL integration and Docker image execution require their own runtime checks.

## What changed from the coursework version

Invalid forms now stop before insertion. A zero grade is valid. Records render correctly, gender values are checked consistently, and ID strings preserve leading zeros. Database credentials are supplied through environment variables instead of source code. The footer markup and grade schema have also been corrected.

## Portfolio walkthrough

Record a short demo: browse sample records, search for a student, add a fictional student, show an invalid grade, edit the record, and confirm deletion. Add the live URL and a screenshot after deployment.
