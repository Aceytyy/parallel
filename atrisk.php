<?php include 'navbar.php'; ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>At-Risk Students</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<h1>At-Risk Students (GPA below 80)</h1>

<table>
    <thead>
        <tr>
            <th>Student</th>
            <th>GPA</th>
        </tr>
    </thead>
    <tbody id="riskBody"></tbody>
</table>

<script>
fetch("get_data.php?type=atRisk")
.then(res => res.json())
.then(data => {
    const tbody = document.getElementById("riskBody");
    tbody.innerHTML = data.map(d => `
        <tr>
            <td>${d.label}</td>
            <td>${d.gpa}</td>
        </tr>
    `).join('');
})
.catch(err => console.error("At-Risk fetch failed:", err));
</script>

</body>
</html>
