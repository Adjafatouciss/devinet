<?php

/**
 * File: index.php
 * Role: Entry point of the application (http://localhost/devinet-jeu-game/).
 *
 * It does not display anything: it only sends the visitor to the right
 * page. The login form is the page that triggers the automatic creation
 * of the database the first time the application is accessed.
 */

// Enable strict typing for the whole file.
declare(strict_types=1);

// Load the shared functions (sessions, redirections...).
require_once __DIR__ . '/includes/functions.php';

// Start or resume the PHP session.
startAppSession();

// A logged-in user goes straight to the game.
if (isLoggedIn()) {
    // Redirect to the game form.
    redirectTo('game-form.php');
}

// Any other visitor goes to the login form.
redirectTo('login-form.php');

// Notes for the reader:
// - redirectTo() calls exit, so no code after it is executed.
// - The session must be started before calling isLoggedIn().
// - The database is created by Database::getConnection(), called in login-form.php.
// - No HTML is sent, so the "Location" header can still be sent.
// - This file keeps the URLs short: the user only types the folder name.
// - Every page of the app is reachable from here, there is no isolated page.
