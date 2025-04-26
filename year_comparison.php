<?php include 'navbar.php'; ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Year Comparison</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<h1>Yearly GPA Comparison</h1>

<table>
    <thead>
        <tr>
            <th>Semester</th>
            <th>Average GPA</th>
        </tr>
    </thead>
    <tbody id="yearBody"></tbody>
</table>

<script>
fetch("get_data.php?type=year")
.then(res => res.json())
.then(data => {
    const tbody = document.getElementById("yearBody");
    tbody.innerHTML = data.map(d => `
        <tr>
            <td>${d.label}</td>
            <td>${d.value}</td>
        </tr>
    `).join('');
})
.catch(err => console.error("Year fetch failed:", err));
</script>

</body>
</html>
