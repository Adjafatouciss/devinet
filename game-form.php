<?php

/**
 * File: game-form.php
 * Role: Front-end page of the GUESSING GAME form.
 *
 * It displays: the current date and time, the username, the score,
 * the result of the last game (if any), the 13 numbers (0 to 12) that
 * can be dragged and dropped into 5 boxes, a "PLAY" button and a
 * "LOG OUT" button. The data is processed by game-form-handling.php.
 */

// Enable strict typing for the whole file.
declare(strict_types=1);

// Load the shared functions (sessions, header, footer...).
require_once __DIR__ . '/includes/functions.php';

// Start or resume the PHP session.
startAppSession();

// Only logged-in users can play.
requireLogin();

// Information of the current user, read from the session.
$username = $_SESSION['username'];
$pointsWon = $_SESSION['points_won'] ?? 0;
$pointsPossible = $_SESSION['points_possible'] ?? 0;

// Current date and time, e.g. "Tuesday October 28, 2025 - 18:03:46 PM".
$currentDateTime = date('l F j, Y - H:i:s A');

// Result of the last game (null if the user has not played yet).
$gameResult = pullFlashValue('game_result');

// The colour of the container depends on the result (CSS class), "match" again.
$containerClass = match ($gameResult['level'] ?? null) {
    'excellent' => 'container-excellent',
    'good'      => 'container-good',
    'bad'       => 'container-bad',
    default     => 'container-game',
};

// Display the <head> and the common <header>.
renderHeader('Guessing Game');
?>
<!-- Main container of the game (the border colour changes with the result) -->
<section class="form-container <?= escapeHtml($containerClass) ?>">

    <!-- Information bar: date and time, user, score -->
    <div class="info-bar">
        <span class="info-badge" id="current-datetime"><?= escapeHtml($currentDateTime) ?></span>
        <span class="info-badge">User : <?= escapeHtml($username) ?></span>
        <span class="info-badge">Score : <?= escapeHtml($pointsWon) ?>/<?= escapeHtml($pointsPossible) ?></span>
    </div>

    <!-- Form sent with POST to the game handling script -->
    <form action="game-form-handling.php" method="post" id="game-form">
        <fieldset>
            <legend>Game Form</legend>

            <!-- Error / success messages (welcome message, validation errors...) -->
            <?php renderMessages(); ?>

            <?php if ($gameResult !== null) : ?>
                <!-- Result of the last game (displayed only after PLAY) -->
                <div class="game-result">
                    <p>1-Generated numbers: <strong><?= escapeHtml(implode(' , ', $gameResult['generated'])) ?></strong></p>
                    <p>2-Guessed numbers: <strong><?= escapeHtml(implode(' , ', $gameResult['guessed'])) ?></strong></p>
                    <p>3-Result: <?= escapeHtml($gameResult['result']) ?></p>
                    <p>4-Feedback: <?= escapeHtml($gameResult['feedback']) ?></p>
                </div>
            <?php endif; ?>

            <!-- Instructions of the game -->
            <p class="instructions">
                This game will generate randomly <?= GAME_NUMBERS_COUNT ?> distinct numbers
                from <?= GAME_MIN_NUMBER ?> to <?= GAME_MAX_NUMBER ?>.
                Guess these <?= GAME_NUMBERS_COUNT ?> numbers to play!
            </p>

            <!-- Pool of numbers: each number can be dragged only once -->
            <div class="number-pool drop-zone" id="number-pool">
                <?php for ($number = GAME_MIN_NUMBER; $number <= GAME_MAX_NUMBER; $number++) : ?>
                    <span class="number-tile" draggable="true" id="tile-<?= $number ?>" data-number="<?= $number ?>"><?= $number ?></span>
                <?php endfor; ?>
            </div>

            <!-- Instructions for the drag and drop -->
            <p class="instructions">Drag and drop your <?= GAME_NUMBERS_COUNT ?> numbers within the boxes below!</p>

            <!-- The 5 boxes: each one has a hidden input filled by js/game.js -->
            <div class="guess-boxes">
                <?php for ($boxIndex = 0; $boxIndex < GAME_NUMBERS_COUNT; $boxIndex++) : ?>
                    <div class="guess-box drop-zone" data-box-index="<?= $boxIndex ?>">
                        <input type="hidden" name="guesses[]" value="">
                    </div>
                <?php endfor; ?>
            </div>

            <!-- Message written by JavaScript when a box is still empty -->
            <p class="message message-error hidden" id="client-message" aria-live="polite"></p>

            <!-- Buttons: PLAY submits this form, LOG OUT submits the logout form -->
            <div class="form-buttons form-buttons-small">
                <button type="submit" class="button button-primary" id="play-button">PLAY</button>
                <button type="submit" class="button button-secondary" form="logout-form">LOG OUT</button>
            </div>
        </fieldset>
    </form>

    <!-- Separate, empty form used by the LOG OUT button (attribute form="logout-form") -->
    <form action="logout.php" method="post" id="logout-form"></form>
</section>
<?php
// Display the common <footer> and load the drag and drop script.
renderFooter(['js/game.js']);
