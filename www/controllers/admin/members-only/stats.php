<?php

require path_to('database/DatabaseQueries.php');
$dbQueries = new DatabaseQueries();
$mostPopularCity = $dbQueries->get_most_popular_city();
$totalPhotos = $dbQueries->get_total_photos();
$flaggedUsers = $dbQueries->get_flagged_users();
$dbQueries->disconnect();

$msg = "This is the stats page.";
$stylesheets = [
    "style1",
];
$page_title = "Stats Dashboard";



require path_to('views/members-only/stats.view.php');
