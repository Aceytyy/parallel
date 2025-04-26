<?php include 'navbar.php'; ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Subject Analytics</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<h1>Subject Analytics by Semester</h1>

<label for="semesterSelect">Select Semester:</label>
<select id="semesterSelect" onchange="loadSubjectAnalytics()">
    <option value="1">Semester 1</option>
    <option value="2">Semester 2</option>
    <option value="3">Semester 3</option>
    <option value="4">Semester 4</option>
    <option value="5">Semester 5</option>
</select>

<table>
    <thead>
        <tr>
            <th>Subject</th>
            <th>Average</th>
            <th>Pass Rate</th>
            <th>Top Grade</th>
            <th>At-Risk %</th>
        </tr>
    </thead>
    <tbody id="subjectBody"></tbody>
</table>

<script>
function loadSubjectAnalytics() {
    const semester = document.getElementById("semesterSelect").value;
    fetch(`get_data.php?type=subject&semester=${semester}`)
    .then(res => res.json())
    .then(data => {
        const tbody = document.getElementById("subjectBody");
        tbody.innerHTML = data.map(d => `
            <tr>
                <td>${d.label}</td>
                <td>${d.average}</td>
                <td>${d.passRate}%</td>
                <td>${d.topGrade}</td>
                <td>${d.atRisk}%</td>
            </tr>
        `).join('');
    })
    .catch(err => console.error("Subject Analytics fetch failed:", err));
}

document.addEventListener('DOMContentLoaded', loadSubjectAnalytics);
</script>

</body>
</html>
