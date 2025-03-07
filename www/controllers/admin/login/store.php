<?php
require path_to('database/DatabaseQueries.php');



// Instantiate the DatabaseQueries class
$dbQuery = new DatabaseQueries();
$admins = $dbQuery->getting_admins();

$username = $_POST['username'];
$password = $_POST['password'];
$remember_me = false;

if (isset($_POST["remember_me"])) {
    $remember_me = true;
}



// Ensure the admins table and default admins exist
// $dbQuery->createAdminsTable();
// $dbQuery->addDefaultAdmins();

// Fetch and validate the user
// $token = $_POST['remember_me_token'] ?? ''; // Ensure you have a token variable from the POST data
// $user = $dbQuery->fetchUserByToken($token); // Use $dbQuery, not $db

// if (!$user) {
//     $_SESSION['error_message'] = 'Invalid username or password.';
//     $_SESSION['old_username'] = $username ?? '';
//     redirect('/admin');
// }


// Check username and password
if ($username === '' || $password === '') {
    $_SESSION['error_message'] = 'Must enter username and password';
    $_SESSION['old_username'] = $username;
    redirect('/admin');
}


$isValid = false;
foreach ($admins as $admin) {
    if ($username == $admin['username'] && password_verify($password, $admin['password'])) {
        $isValid = true;
    }
}

if ($isValid == false) {
    $_SESSION['error_message'] = 'Invalid username or password.';
    $_SESSION['old_username'] = $username;
    redirect('/admin');
}

// Login successful: Set session and optional remember-me cookie
if ($remember_me) {
    $token = bin2hex(random_bytes(32));
    $expiry = date('Y-m-d H:i:s', strtotime('+30 days'));

    // Store token in the database
    $dbQuery->storeUserToken($user['id'], $token, $expiry);

    // Set cookie
    setcookie('remember_me', $token, time() + (30 * 24 * 60 * 60), '/', '', false, true);
}

// $_SESSION['user_id'] = $user['id'];
$_SESSION['authorized'] = true;

$dbQuery->disconnect(); // Clean up database connection if necessary

redirect('/admin/dashboard/stats');
