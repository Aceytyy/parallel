<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Student Performance Dashboard</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body {
            background-color: #1e1e2f;
            color: white;
            font-family: Arial, sans-serif;
        }
        .tab {
            padding: 10px;
            margin: 5px;
            background-color: #333;
            border: none;
            color: white;
            cursor: pointer;
        }
        .tab.active {
            background-color: #007bff;
        }
        table {
            width: 100%;
            margin-top: 10px;
            border-collapse: collapse;
            color: white;
        }
        th, td {
            padding: 8px;
            border: 1px solid #444;
            text-align: center;
        }
        select, input {
            padding: 5px;
            margin-top: 10px;
        }
    </style>
</head>
<body>

<h2><img src="https://img.icons8.com/ios-filled/50/ffffff/combo-chart.png" width="28"> Student Performance Dashboard</h2>

<button class="tab active" onclick="showTab('performance')">Student Performance</button>
<button class="tab" onclick="showTab('risk')">At-Risk Students</button>
<button class="tab" onclick="showTab('subject')">Subject Analytics</button>
<button class="tab" onclick="showTab('year')">Year Comparison</button>

<!-- Performance -->
<div id="performanceTab">
    <h3>Student Performance Report</h3>
    <input type="text" id="studentSearch" placeholder="Enter Student ID" />
    <button onclick="loadStudentPerformance()">Search</button>
    
    <div id="studentInfo" style="margin-top: 20px;"></div>

    <table id="performanceTable">
        <thead>
            <tr>
                <th>Subject Code</th>
                <th>Description</th>
                <th>Student Grade</th>
                <th>Class Average</th>
                <th>Rate</th>
            </tr>
        </thead>
        <tbody id="performanceBody"></tbody>
    </table>
</div>

<!-- Subject Analytics -->
<div id="subjectTab" style="display:none;">
    <label for="semesterSelect">Select Semester:</label>
    <select id="semesterSelect" onchange="loadSubjectData()">
        <option value="1">Semester 1</option>
        <option value="2">Semester 2</option>
        <option value="3">Semester 3</option>
        <option value="4">Semester 4</option>
        <option value="5">Semester 5</option>
    </select>

    <table id="subjectTable">
        <thead>
            <tr>
                <th>Subject Code</th>
                <th>Description</th>
                <th>Average Grade</th>
                <th>Passing Rate (%)</th>
                <th>Top Grade</th>
                <th>At-Risk Students (%)</th>
            </tr>
        </thead>
        <tbody id="subjectBody"></tbody>
    </table>
</div>

<script>
function showTab(tab) {
    document.querySelectorAll('.tab').forEach(t => t.classList.remove('active'));
    document.querySelectorAll('div[id$="Tab"]').forEach(div => div.style.display = 'none');

    document.querySelector(`.tab[onclick*="${tab}"]`).classList.add('active');
    document.getElementById(`${tab}Tab`).style.display = 'block';

    if (tab === 'subject') loadSubjectData();
}

function loadStudentPerformance() {
    const id = document.getElementById("studentSearch").value;
    fetch(`get_data.php?type=performance&studentId=${id}`)
        .then(res => res.json())
        .then(data => {
            const tbody = document.getElementById("performanceBody");
            const info = document.getElementById("studentInfo");
            tbody.innerHTML = "";

            if (!data.subjects || data.subjects.length === 0) {
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

            info.innerHTML = `
                <strong>GPA:</strong> ${gpa} &nbsp;&nbsp;
                <strong>Z-Score:</strong> ${zScore}
                <br><br>
                <div style="background: #2f2f4f; width: 300px; height: 20px;">
                    <div style="background: lightblue; width: ${(gpa / 5) * 100}%; height: 100%;"></div>
                </div>
            `;

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
        .catch(err => console.error("Failed to load performance data:", err));
}

function loadSubjectData() {
    const semester = document.getElementById("semesterSelect").value;
    fetch(`get_data.php?type=subject&semester=${semester}`)
        .then(res => res.json())
        .then(data => {
            const tbody = document.getElementById("subjectBody");
            tbody.innerHTML = "";
            if (Array.isArray(data)) {
                data.forEach(row => {
                    const tr = document.createElement("tr");
                    tr.innerHTML = `
                        <td>${row.label}</td>
                        <td>${row.description}</td>
                        <td>${row.average}</td>
                        <td>${row.passRate}%</td>
                        <td>${row.topGrade}</td>
                        <td>${row.atRisk}%</td>
                    `;
                    tbody.appendChild(tr);
                });
            }
        });
}

document.addEventListener('DOMContentLoaded', () => {
    loadStudentPerformance();
});
</script>

</body>
</html>
