<?php

/**
 * File: registration-form.php
 * Role: Front-end page of the REGISTRATION (sign up) form.
 *
 * It contains: 3 inputs (Username, Password, Confirm Password), a "SIGN UP"
 * button that submits the form and a "LOG IN" button that opens the login page.
 * While typing the username, js/registration.js checks in real time (AJAX)
 * whether the username already exists.
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

// The database is also created here if this page is the first one visited.
try {
    // Opening the connection runs the automatic creation of the database.
    Database::getConnection();
} catch (DatabaseException $exception) {
    // The error is displayed inside the page (no isolated error page).
    addMessage('error', $exception->getDisplayMessage());
}

// Retrieve the username typed before an error.
$previousUsername = (string) pullFlashValue('registration_username', '');

// Zone where the AJAX script writes "This username already exists".
$ajaxMessageZone = '<span id="username-feedback" class="field-feedback" aria-live="polite"></span>';

// Zone where the JavaScript writes the live password comparison.
$passwordMessageZone = '<span id="password-feedback" class="field-feedback" aria-live="polite"></span>';

// Display the <head> and the common <header>.
renderHeader('Sign Up');
?>
<!-- Main container of the registration form (green border) -->
<section class="form-container container-auth">
    <h2>Registration Form</h2>

    <!-- Error / success messages coming from the PHP handling script -->
    <?php renderMessages(); ?>

    <!-- The form is sent with POST to the handling script -->
    <form action="registration-form-handling.php" method="post" class="auth-form" id="registration-form">
        <fieldset>
            <legend>Sign Up</legend>

            <!-- Input: Username + zone for the real-time AJAX message -->
            <?php renderInputRow('Username', 'username', 'text', $previousUsername, $ajaxMessageZone); ?>

            <!-- Input: Password -->
            <?php renderInputRow('Password', 'password', 'password'); ?>

            <!-- Input: Confirm Password + zone for the live comparison message -->
            <?php renderInputRow('Confirm Password', 'confirm_password', 'password', '', $passwordMessageZone); ?>

            <!-- Buttons: submit the form / go to the login form -->
            <div class="form-buttons">
                <button type="submit" class="button button-primary" id="signup-button">SIGN UP</button>
                <a href="login-form.php" class="button button-secondary">LOG IN</a>
            </div>
        </fieldset>
    </form>
</section>
<?php
// Display the common <footer> and load the AJAX script of this page.
renderFooter(['js/registration.js']);
