<?php
require 'mongo_connection.php';

$cursor = $db->grades->aggregate([
    ['$unwind' => '$SubjectCodes'],
    ['$unwind' => '$Grades'],
    ['$group' => [
        '_id' => '$SubjectCodes',
        'avgGrade' => ['$avg' => '$Grades'],
        'passRate' => [
            '$avg' => [
                '$cond' => [['$gte' => ['$Grades', 75]], 1, 0]
            ]
        ]
    ]],
    ['$sort' => ['_id' => 1]]
]);

$data = [];
foreach ($cursor as $doc) {
    $data[] = [
        'subject' => $doc['_id'],
        'average' => round($doc['avgGrade'], 2),
        'passRate' => round($doc['passRate'] * 100, 2)
    ];
}

header('Content-Type: application/json');
echo json_encode($data);
