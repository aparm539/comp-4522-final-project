<?php

// Commenting this to get the routing logic to work
// die();

require_once '../routes/web.php'; // Assuming 'web.php' is your routes file
$router->route($_SERVER['REQUEST_URI'], $_SERVER['REQUEST_METHOD']);
