<?php include 'navbar.php'; ?>
<?php require 'mongo_connection.php'; ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dashboard - Student Analytics</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<h1>Dashboard Overview</h1>

<div class="summary-cards">
    <div class="card">
        <h3>Total Students</h3>
        <p id="totalStudents">Loading...</p>
    </div>
    <div class="card">
        <h3>Average GPA</h3>
        <p id="avgGPA">Loading...</p>
    </div>
    <div class="card">
        <h3>At-Risk Students</h3>
        <p id="riskCount">Loading...</p>
    </div>
    <div class="card">
        <h3>Top Subject</h3>
        <p id="topSubject">Loading...</p>
    </div>
</div>

<script>
fetch("get_data.php?type=summary")
.then(res => res.json())
.then(data => {
    document.getElementById("totalStudents").innerText = data.totalStudents;
    document.getElementById("avgGPA").innerText = data.averageGPA;
    document.getElementById("riskCount").innerText = data.atRiskCount;
    document.getElementById("topSubject").innerText = data.topSubject;
})
.catch(err => console.error("Summary fetch failed:", err));
</script>

</body>
</html>
