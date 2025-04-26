<?php include 'navbar.php'; ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Student Performance</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<h1>Student Performance Report</h1>

<input type="text" id="studentSearch" placeholder="Enter Student ID" />
<button onclick="loadStudentPerformance()">Search</button>

<div id="studentInfo" style="margin-top:20px;"></div>

<table id="performanceTable">
    <thead>
        <tr>
            <th>Subject Code</th>
            <th>Description</th>
            <th>Grade</th>
            <th>Class Average</th>
            <th>Rate</th>
        </tr>
    </thead>
    <tbody id="performanceBody"></tbody>
</table>

<script>
function loadStudentPerformance() {
    const id = document.getElementById("studentSearch").value;
    fetch(`get_data.php?type=performance&studentId=${id}`)
    .then(res => res.json())
    .then(data => {
        const tbody = document.getElementById("performanceBody");
        const info = document.getElementById("studentInfo");
        tbody.innerHTML = "";
        if (!data.subjects.length) {
            info.innerHTML = `<p>No data found for Student ID: ${id}</p>`;
            return;
        }
        let sum = 0;
        data.subjects.forEach(row => sum += row.grade);
        const gpa = (sum / data.subjects.length).toFixed(2);

        const classAvg = data.subjects.reduce((a, b) => a + b.classAverage, 0) / data.subjects.length;
        const variance = data.subjects.reduce((a, b) => a + Math.pow(b.classAverage - classAvg, 2), 0) / data.subjects.length;
        const stdDev = Math.sqrt(variance);
        const zScore = ((gpa - classAvg) / stdDev).toFixed(2);

        info.innerHTML = `<b>GPA:</b> ${gpa} &nbsp;&nbsp; <b>Z-Score:</b> ${zScore}`;

        data.subjects.forEach(row => {
            const tr = document.createElement("tr");
            tr.innerHTML = `
                <td>${row.code}</td>
                <td>${row.description}</td>
                <td>${row.grade}</td>
                <td>${row.classAverage}</td>
                <td>${row.rate}</td>
            `;
            tbody.appendChild(tr);
        });
    })
    .catch(err => console.error("Performance fetch failed:", err));
}
</script>

</body>
</html>
