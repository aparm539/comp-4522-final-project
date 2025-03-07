<?php
require path_to('database/DatabaseQueries.php');

$query = new DatabaseQueries();

$name = $_GET['name'];
$type = $_GET['type'];
$flag = $query->all_flagged_images();

if ($type === "city") {
    $matchingImages = $query->matching_city($name);
} elseif ($type == "country") {
    $matchingImages = $query->matching_country($name);
} else {
    $matchingImages = $query->matching_continent($name);
}

$query->disconnect();

$response = [];
foreach ($matchingImages as $photo) {
    // $flagResult = $query->present_in_table($photo['ImageID'], $flag);
    $response[] = [
        'imageID' => $photo['ImageID'],
        'path' => $photo['Path'],
        'country' => $photo['CountryName'],
        'city' => $photo['City'],
        'fullname' => $photo['FirstName'] . " " . $photo['LastName'],
        // 'flag' => $flagResult
    ];
}

http_response_code(200);
header('Content-type: application/JSON');
echo json_encode($response);
