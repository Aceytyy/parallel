<?php
require 'mongo_connection.php';

$cursor = $db->grades->aggregate([
    ['$group' => [
        '_id' => '$SemesterID',
        'avgGPA' => ['$avg' => [
            '$avg' => '$Grades'
        ]]
    ]],
    ['$sort' => ['_id' => 1]]
]);

$data = [];
foreach ($cursor as $doc) {
    $data[] = [
        'semester' => 'Semester ' . $doc['_id'],
        'avgGPA' => round($doc['avgGPA'], 2)
    ];
}

header('Content-Type: application/json');
echo json_encode($data);
