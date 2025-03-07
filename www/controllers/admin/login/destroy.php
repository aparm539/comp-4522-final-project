<?php

// This controller is used when the Logout link is clicked.
// It gets rid of all the session variable, requests that the PHPSESSID
// cookie go buh-bye, and redirects back to /.



$dbQueries = new DatabaseQueries();

// Clear session data
// Clear remember-me cookie
if (isset($_COOKIE['remember_me'])) {
    $db->deleteUserToken($_COOKIE['remember_me']);
    setcookie('remember_me', '', time() - 3600, '/', '', false, true); // Expire the cookie
    session_unset();
    session_destroy();
}

nuke_session();
redirect('/admin');
