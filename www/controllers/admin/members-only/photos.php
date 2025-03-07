<?php

require path_to('database/DatabaseQueries.php');
$dbQueries = new DatabaseQueries();
$photos = $dbQueries->get_photos_with_users();
$dbQueries->disconnect();

$msg = "This is the photos page.";
$stylesheets = [
    "style2",
];
$page_title = "Photo Dashboard";


require path_to('views/members-only/photos.view.php');
