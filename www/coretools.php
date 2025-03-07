<?php

define('PROJECT_ROOT', __DIR__);

/**
 * Helps get you out of relative path hell.
 * Provides the proper path to a given file, rooted at www (on this Codespace).
 * 
 * Example of use: 
 *   path_to('core/Router.php') gives you the proper path to Router.php, 
 *   no matter what directory you're in.
 */
function path_to($resource) {
    return PROJECT_ROOT . "/$resource";
}

// "Dump and Die"
// Useful for those situations where you want to do a trace statement,
// and then want to immediately stop execution so that other things later
// on in the code don't happen!
function dnd($var, $title = "") {
    dump($var, $title);
    die();
}


// Got this from a Laracasts episode at one point? Or a php.net comment?
// Modified it a bit, because OCD.
// Yes, this function totally violates my suggestion of "don't echo markup!"
function dump($var, $title = "") {
    // Put a horizontal rule in to visually separate things in browser.
    echo "<hr>";

    // If a title was passed in, use it.
    if ($title) {
        echo "<p><b>$title</b>:</p>";
    }

    // Put the variable between <pre> tags for formatting purposes.
    // If the thing coming in is just a string, no need for var_dump().
    echo "<pre>";
    if (gettype($var) === "string") {
        echo $var;
    } else {
        var_dump($var);
    }
    echo "</pre>";

    echo "<hr>";
}

/**
 * Useful for styling navigation - returns true iff the given target
 * matches the current URI.
 * 
 * Saw this on Laracasts at some point. Stupid memory....
 */
function uri_matches($target) {
    return $_SERVER['REQUEST_URI'] === $target;
}

// Modified from: https://www.php.net/manual/en/function.session-destroy.php#example-4820
// 
// Assumption: session_start() has been called prior to this call.
function nuke_session() {

    // Clear all session variables.
    $_SESSION = [];


    // Wipe out the PHPSESSID cookie on the browser side.
    $params = session_get_cookie_params();
    setcookie('PHPSESSID', '', strtotime("+ 30 days"), $params['path'], $params['domain'], $params['secure'], $params['httponly']);

    session_destroy();
}

function redirect($location) {
    header("Location: {$location}");
    exit();
}
