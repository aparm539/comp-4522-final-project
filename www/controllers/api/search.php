<?php
require_once '../database/DatabaseQueries.php';

$dbQuery = new DatabaseQueries();

$data = $_GET['query'] ?? '';

$countryResults = $dbQuery->search_country($data);
$cityResults = $dbQuery->search_city($data);
$continentResults = $dbQuery->search_continent($data);
$UserNames = $dbQuery->search_country($data);
$UserNames = $dbQuery->search_city($data);
$UserNames = $dbQuery->search_continent($data);
$dbQuery->disconnect();

$response = [];

foreach ($countryResults as $result) {
    $response[] = [
        'name' => $result['name'],
        'numOfImages' => $result['photoCount'],
        'UserNames' => $result['UserNames'],
        'type' => 'country'
    ];
}

foreach ($cityResults as $result) {
    $response[] = [
        'name' => $result['name'],
        'numOfImages' => $result['photoCount'],
        'UserNames' => $result['UserNames'],
        'type' => 'city'
    ];
}

foreach ($continentResults as $result) {
    $response[] = [
        'name' => $result['name'],
        'numOfImages' => $result['photoCount'],
        'UserNames' => $result['UserNames'],
        'type' => 'continent'
    ];
}

http_response_code(200);
header('Content-Type: application/json');
echo json_encode($response);
