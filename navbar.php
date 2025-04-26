<?php
$current = basename($_SERVER['PHP_SELF']);
?>

<div style="display: flex; gap: 10px; padding: 20px; background-color: #1b1b2f;">
    <a href="dashboard.php" class="<?= $current == 'dashboard.php' ? 'active' : '' ?>">Dashboard</a>
    <a href="student_performance.php" class="<?= $current == 'student_performance.php' ? 'active' : '' ?>">Student Performance</a>
    <a href="atrisk.php" class="<?= $current == 'atrisk.php' ? 'active' : '' ?>">At-Risk Students</a>
    <a href="subject_analytics.php" class="<?= $current == 'subject_analytics.php' ? 'active' : '' ?>">Subject Analytics</a>
    <a href="year_comparison.php" class="<?= $current == 'year_comparison.php' ? 'active' : '' ?>">Year Comparison</a>
</div>

<style>
a {
  padding: 10px 15px;
  color: white;
  text-decoration: none;
  font-weight: bold;
  background-color: #162447;
  border-radius: 5px;
  transition: background-color 0.3s;
}
a:hover {
  background-color: #1f4068;
}
a.active {
  background-color: #4CAF50;
}
</style>
