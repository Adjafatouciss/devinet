<?php

/**
 * File: login-form-handling.php
 * Role: Back-end processing of the LOGIN form.
 *
 * Steps: 1) read the data, 2) validate it, 3) compare it with the
 * database, 4) open the session and go to the game, or go back to
 * the login form with the message "The username or password is incorrect."
 */

// Enable strict typing for the whole file.
declare(strict_types=1);

// Load the shared functions and the UserManager class.
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/classes/UserManager.php';

// Start or resume the PHP session.
startAppSession();

// This script only accepts data sent by the form (POST method).
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirectTo('login-form.php');
}

// 1) Read the received data (empty string if a field is missing).
$username = trim((string) ($_POST['username'] ?? ''));
$password = (string) ($_POST['password'] ?? '');

// Keep the username so the form can be pre-filled after an error.
setFlashValue('login_username', $username);

// 2) Both fields are mandatory.
if ($username === '' || $password === '') {
    addMessage('error', 'Please enter your username and your password.');
    redirectTo('login-form.php');
}

// 3) Compare the information with the database (protected by try/catch).
try {
    // Create the object that manages the user accounts.
    $userManager = new UserManager(Database::getConnection());

    // Check the username AND the password.
    $isValidLogin = $userManager->verifyCredentials($username, $password);
} catch (DatabaseException $exception) {
    // A database problem is displayed in the login form.
    addMessage('error', $exception->getDisplayMessage());
    redirectTo('login-form.php');
}

// 4a) Wrong information: same message for both cases (more secure).
if (!$isValidLogin) {
    addMessage('error', 'The username or password is incorrect.');
    redirectTo('login-form.php');
}

// 4b) Correct information: the user is logged in and the score starts at 0/0.
logUserIn($username);

// The pre-filled username is no longer needed.
pullFlashValue('login_username');

// Welcome message displayed in the game form.
addMessage('success', 'Welcome ' . $username . '! Good luck!');

// Go to the game form.
redirectTo('game-form.php');
