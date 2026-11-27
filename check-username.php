<?php

/**
 * File: check-username.php
 * Role: Server side of the AJAX real-time check.
 *
 * js/registration.js calls this file with XMLHttpRequest each time the
 * user types in the "Username" field:  check-username.php?username=abc
 * The answer is a small JSON object, for example: {"exists": true}
 */

// Enable strict typing for the whole file.
declare(strict_types=1);

// Load the UserManager class (it also loads the configuration and Database).
require_once __DIR__ . '/classes/UserManager.php';

// The answer is JSON, not HTML.
header('Content-Type: application/json; charset=utf-8');

// The answer must never be cached by the browser (it changes in real time).
header('Cache-Control: no-store');

// Read the username sent in the URL and remove the spaces around it.
$username = trim((string) ($_GET['username'] ?? ''));

// An empty username can't exist: answer immediately without querying MySQL.
if ($username === '') {
    echo json_encode(['exists' => false]);
    exit;
}

// Query the database (protected by try/catch).
try {
    // Create the object that manages the user accounts.
    $userManager = new UserManager(Database::getConnection());

    // Ask the database whether the username is already taken.
    $usernameExists = $userManager->usernameExists($username);

    // Send the answer to the JavaScript code.
    echo json_encode(['exists' => $usernameExists]);
} catch (DatabaseException $exception) {
    // HTTP code 500 = server error.
    http_response_code(500);

    // The JavaScript code displays this message under the Username field.
    echo json_encode(['error' => $exception->getDisplayMessage()]);
}

// Notes for the reader:
// - json_encode() converts a PHP array into a JSON string.
// - The GET method is used because the request only reads data.
// - The final check is still done in registration-form-handling.php (never trust the browser).
