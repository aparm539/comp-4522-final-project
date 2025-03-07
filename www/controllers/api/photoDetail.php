<?php
require path_to('database/DatabaseQueries.php');

$query = new DatabaseQueries();

$detail = $_GET['photoDetail'];
$type = $_GET['type'];

$response = [];

foreach ($photoDetailImage as $photo) {
    $response[] = [
        'imageID' => $photo['ImageID'],
    ];
}


$query->disconnect();

http_response_code(200);
header('Content-Type: application/json');
echo json_encode($response);
