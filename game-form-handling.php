<?php

/**
 * File: game-form-handling.php
 * Role: Back-end processing of the GUESSING GAME form.
 *
 * Steps: 1) validate the 5 guessed numbers, 2) generate 5 random distinct
 * numbers, 3) compare both lists, 4) update the score
 * ("points won / possible points"), 5) send the result to game-form.php.
 */

// Enable strict typing for the whole file.
declare(strict_types=1);

// Load the shared functions and the GuessingGame class.
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/classes/GuessingGame.php';

// Start or resume the PHP session.
startAppSession();

// Only logged-in users can play.
requireLogin();

// This script only accepts data sent by the form (POST method).
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirectTo('game-form.php');
}

// Create the game object with the default rules (5 numbers from 0 to 12).
$game = new GuessingGame();

// 1) Validate the guessed numbers received from the hidden inputs.
try {
    // validateGuesses() throws an exception if the data is not valid.
    $guessedNumbers = $game->validateGuesses($_POST['guesses'] ?? null);
} catch (InvalidArgumentException $exception) {
    // Display the validation problem in the game form.
    addMessage('error', $exception->getMessage());
    redirectTo('game-form.php');
}

// 2) and 3) Generate the secret numbers and compare them with the guesses.
$gameResult = $game->play($guessedNumbers);

// 4) Update the score: +1 point per number guessed successfully...
$_SESSION['points_won'] = ($_SESSION['points_won'] ?? 0) + count($gameResult['matched']);

// ...and +5 to the total of possible points.
$_SESSION['points_possible'] = ($_SESSION['points_possible'] ?? 0) + GAME_NUMBERS_COUNT;

// 5) Keep the result for the next page (it is displayed only once).
setFlashValue('game_result', $gameResult);

// Go back to the game form, which displays the result and the new score.
redirectTo('game-form.php');

// Notes for the reader:
// - Redirecting after a POST (Post/Redirect/Get pattern) prevents the
//   browser from replaying the same game when the page is refreshed.
// - The generated numbers are created on the server only, so the player
//   cannot read them in the HTML code before playing.
