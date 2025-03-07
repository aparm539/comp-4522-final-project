<?php
require path_to('database/DatabaseQueries.php');

$query = new DatabaseQueries();

$detail = $_GET['countryDetail'];
$type = $_GET['type'];


$response = [];

foreach ($countryDetailImage as $photo) {
    $response[] = [
        'imageID' => $photo['ImageID'],
    ];
}



$query->disconnect();

http_response_code(200);
header('Content-Type: application/json');
echo json_encode($response);
