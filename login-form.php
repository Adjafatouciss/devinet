<?php

/**
 * File: login-form.php
 * Role: Front-end page of the LOGIN form.
 *
 * It contains: 2 inputs (Username, Password), a "LOG IN" button that
 * submits the form and a "SIGN UP" button that opens the registration page.
 * The data is processed by login-form-handling.php.
 */

// Enable strict typing for the whole file.
declare(strict_types=1);

// Load the shared functions and the Database class.
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/classes/Database.php';

// Start or resume the PHP session.
startAppSession();

// A user that is already logged in does not need this page.
requireGuest();

// First access: the database, the table and the test data are created here.
try {
    // Opening the connection runs the automatic creation of the database.
    Database::getConnection();
} catch (DatabaseException $exception) {
    // The error is displayed inside the page (no isolated error page).
    addMessage('error', $exception->getDisplayMessage());
}

// Retrieve the username typed before an error, so the user doesn't retype it.
$previousUsername = (string) pullFlashValue('login_username', '');

// Display the <head> and the common <header>.
renderHeader('Log In');
?>
<!-- Main container of the login form (green border) -->
<section class="form-container container-auth">
    <h2>Login Form</h2>

    <!-- Error / success messages coming from the PHP handling scripts -->
    <?php renderMessages(); ?>

    <!-- The form is sent with POST so the password never appears in the URL -->
    <form action="login-form-handling.php" method="post" class="auth-form">
        <fieldset>
            <legend>Log In</legend>

            <!-- Input: Username (pre-filled after an error) -->
            <?php renderInputRow('Username', 'username', 'text', $previousUsername); ?>

            <!-- Input: Password (never pre-filled for security reasons) -->
            <?php renderInputRow('Password', 'password', 'password'); ?>

            <!-- Buttons: submit the form / go to the registration form -->
            <div class="form-buttons">
                <button type="submit" class="button button-primary">LOG IN</button>
                <a href="registration-form.php" class="button button-secondary">SIGN UP</a>
            </div>
        </fieldset>
    </form>
</section>
<?php
// Display the common <footer> and close the page.
renderFooter();
