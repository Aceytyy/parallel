<?php
require 'mongo_connection.php';

$gradesCollection = $db->grades;
$summaryCollection = $db->gpa_summary;
$summaryCollection->drop(); // Clear old data

$batchSize = 10000;
$totalDocs = $gradesCollection->countDocuments();
$processed = 0;

echo "⏳ Computing GPA summary...\n";

while ($processed < $totalDocs) {
    $pipeline = [
        [
            '$skip' => $processed
        ],
        [
            '$limit' => $batchSize
        ],
        [
            '$project' => [
                'StudentID' => 1,
                'SemesterID' => 1,
                'GradesArray' => [
                    '$cond' => [
                        'if' => ['$isArray' => '$Grades'],
                        'then' => '$Grades',
                        'else' => [
                            '$map' => [
                                'input' => ['$objectToArray' => '$Grades'],
                                'as' => 'g',
                                'in' => ['toDouble' => '$$g.v']
                            ]
                        ]
                    ]
                ]
            ]
        ],
        [
            '$unwind' => '$GradesArray'
        ],
        [
            '$group' => [
                '_id' => [
                    'StudentID' => '$StudentID',
                    'SemesterID' => '$SemesterID'
                ],
                'Total' => ['$sum' => '$GradesArray'],
                'Count' => ['$sum' => 1]
            ]
        ],
        [
            '$project' => [
                '_id' => 0,
                'StudentID' => '$_id.StudentID',
                'SemesterID' => '$_id.SemesterID',
                'GPA' => ['$round' => [['$divide' => ['$Total', '$Count']], 2]]
            ]
        ],
        [
            '$merge' => [
                'into' => 'gpa_summary',
                'whenMatched' => 'merge',
                'whenNotMatched' => 'insert'
            ]
        ]
    ];

    $gradesCollection->aggregate($pipeline);
    $processed += $batchSize;
    echo "✅ Processed $processed / $totalDocs\n";
}

echo "🎉 GPA summary generated in 'gpa_summary' collection.\n";
?>
