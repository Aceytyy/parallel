
# Distributed Student Performance Analytics Dashboard (PHP + MongoDB)

## Project Overview

This is a **web-based student performance analytics dashboard** built using PHP, MongoDB, and Chart.js. It is designed to handle and visualize academic data from a MongoDB database containing over 7 million records. This project was built for the course **CSELEC 3 – Parallel and Distributed Computing**.

## 🧩 System Requirements

- PHP 8.0+
- Composer (PHP dependency manager)
- MongoDB server (tested on MongoDB 4.4+)
- XAMPP (optional for local server setup)
- MongoDB PHP extension (`php_mongodb.dll`)
- Composer package: `mongodb/mongodb`
- Web browser (Chrome, Firefox)

## ✅ Implemented Features

| Feature                         | Status | Description                                                                 |
|--------------------------------|--------|-----------------------------------------------------------------------------|
| GPA Summary Storage            | ✅ Done | Computes weighted GPA per student per semester into `gpa_summary`          |
| GPA Distribution Visualization | ✅ Done | Chart of GPA per student/semester using Chart.js                           |
| At-Risk Student List           | ✅ Done | Lists students with GPA < 75                                               |
| Subject Analytics by Semester | ✅ Done | View average grade, pass rate, top grade, at-risk percentage per subject   |
| Year Comparison Report         | ✅ Done | Visualizes GPA trend across semesters                                      |
| Student Performance Report     | ✅ Done | Detailed subject-level grade view for searched student                     |
| UI Tabbed Interface            | ✅ Done | Clean dark-mode tab navigation per analytics module                        |
| Semester Filtering             | ✅ Done | Dropdown filters Subject Analytics by semester                             |

## 📊 Dataset Structure

MongoDB Collections:

- `grades`: Contains student grades with fields: `StudentID`, `SemesterID`, `Grades`, `SubjectCodes`
- `students`: List of student records (`StudentID`, `Name`)
- `subjects`: Maps subject codes to names (`_id`, `Description`)
- `gpa_summary`: Cached GPA summary per student per semester

## What's Next

-  **Pagination & Lazy Loading** for large reports
- **Outlier Detection (Z-Score)** in reports
- **Admin Analytics Dashboard Cards**
-  **UI Polish** based on final wireframe
-  **Caching** (Redis or Precomputed Collections) – postponed for now

## Folder Structure

```
/Parallel/
├── index.php                 # Main dashboard
├── get_data.php              # API handler
├── mongo_connection.php      # MongoDB connector
├── compute_gpa_summary.php   # GPA summary batch processor
├── style.css                 # Dark mode UI styling
├── README.md                 # This documentation
```

## 💡 Notes

- No insert/update/delete operations. This dashboard is for **display only**.
- Built with performance in mind (aggregation pipelines, summary caching).
- UI inspired by Github’s dark theme.

---

Maintained by Ace Advincula, Shelou Asaria, and Chrissandra Marchelle Bautista for CSELEC 3 — Notre Dame of Marbel University.
