<?php
require path_to('database/DatabaseQueries.php');
$dbQuery = new DatabaseQueries();
$admins = $dbQuery->getting_admins();
$dbQuery->disconnect();

$msg = "This is the login page.";
$stylesheets = [
    "style",
];
$page_title = "PhotoVoyage Login";

if (isset($_COOKIE['remember_me'])) {
    $token = $_COOKIE['remember_me'];
    $admin = $dbQueries->validateUserToken($token);
    if ($admin) {
        $_SESSION['authorized'] = true;
        redirect('/admin/dashboards/stats');
    }
}

$data = [];
$error_message = [];
$data['username'] = "";

if (isset($_SESSION['error_message'])) {
    $error_message = $_SESSION['error_message'];
    unset($_SESSION['error_message']);
}

if (isset($_SESSION['data'])) {
    $data = $_SESSION['data'];
    unset($_SESSION['data']);
}

require path_to('views/login/create.view.php');
