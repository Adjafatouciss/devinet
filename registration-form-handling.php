<?php

/**
 * File: registration-form-handling.php
 * Role: Back-end processing of the REGISTRATION form.
 *
 * Checks: required fields, maximum length, identical passwords
 * ("You have entered 2 different passwords.") and unique username
 * ("This username already exists."). When everything is valid, the
 * account is saved in MySQL and the user is sent to the login form.
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
    redirectTo('registration-form.php');
}

// 1) Read the received data (empty string if a field is missing).
$username = trim((string) ($_POST['username'] ?? ''));
$password = (string) ($_POST['password'] ?? '');
$confirmPassword = (string) ($_POST['confirm_password'] ?? '');

// Keep the username so the form can be pre-filled after an error.
setFlashValue('registration_username', $username);

// List of the validation errors found.
$errors = [];

// 2) The username is mandatory and must fit in the VARCHAR(190) column.
if ($username === '') {
    $errors[] = 'Please enter a username.';
} elseif (mb_strlen($username) > USERNAME_MAX_LENGTH) {
    $errors[] = 'The username must not exceed ' . USERNAME_MAX_LENGTH . ' characters.';
}

// 3) Both passwords are mandatory.
if ($password === '' || $confirmPassword === '') {
    $errors[] = 'Please enter and confirm your password.';
} elseif ($password !== $confirmPassword) {
    // 4) The 2 passwords must be identical.
    $errors[] = 'You have entered 2 different passwords.';
}

// 5) Database checks and insertion (protected by try/catch).
try {
    // Create the object that manages the user accounts.
    $userManager = new UserManager(Database::getConnection());

    // The username must be different from every username already saved.
    if ($username !== '' && $userManager->usernameExists($username)) {
        $errors[] = 'This username already exists.';
    }

    // Create the account only if no error was found.
    if ($errors === []) {
        $userManager->createUser($username, $password);
    }
} catch (DatabaseException $exception) {
    // A database problem is added to the list of errors.
    $errors[] = $exception->getDisplayMessage();
}

// 6a) At least one error: go back to the registration form and display them.
if ($errors !== []) {
    foreach ($errors as $error) {
        addMessage('error', $error);
    }
    redirectTo('registration-form.php');
}

// 6b) Success: the pre-filled value is no longer needed.
pullFlashValue('registration_username');

// The new username is pre-filled in the login form.
setFlashValue('login_username', $username);

// Confirmation message displayed in the login form.
addMessage('success', 'Your account "' . $username . '" has been created. You can now log in.');

// Go to the login form.
redirectTo('login-form.php');
