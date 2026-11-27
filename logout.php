<?php

/**
 * File: logout.php
 * Role: Logs the current user out and sends them back to the login form.
 *
 * It is called by the "LOG OUT" button of the game form.
 * The whole session (username and score) is destroyed.
 */

// Enable strict typing for the whole file.
declare(strict_types=1);

// Load the shared functions (sessions, redirections, messages).
require_once __DIR__ . '/includes/functions.php';

// Resume the current session so it can be destroyed.
startAppSession();

// Remember the username to say goodbye by name.
$username = $_SESSION['username'] ?? '';

// 1) Remove every value stored in the session (username, score...).
$_SESSION = [];

// 2) Delete the session cookie in the browser.
if (ini_get('session.use_cookies')) {
    // Read the parameters used to create the cookie.
    $cookieParams = session_get_cookie_params();

    // A cookie with a date in the past is deleted by the browser.
    setcookie(session_name(), '', time() - 3600, $cookieParams['path'], $cookieParams['domain'], $cookieParams['secure'], $cookieParams['httponly']);
}

// 3) Destroy the session on the server.
session_destroy();

// Start a new, empty session only to carry the goodbye message.
startAppSession();

// Give this new session a brand-new identifier (and a new cookie).
session_regenerate_id(true);

// Message displayed in the login form.
addMessage('success', $username !== '' ? 'Goodbye ' . $username . ', you are now logged out.' : 'You are now logged out.');

// Go back to the login form page (required by the statement).
redirectTo('login-form.php');

// Notes for the reader:
// - The score is kept in the session, so it restarts at 0/0 at the next login.
// - The POST method is used so that a simple link cannot log the user out.
// - redirectTo() calls exit: nothing is executed after this line.
