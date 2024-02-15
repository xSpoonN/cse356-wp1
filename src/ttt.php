<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TTT Page</title>
    <link rel="stylesheet" href="style-ttt.css">
</head>
<body>

<?php
function checkWinner($board, $player)
{
    // Check rows, columns, and diagonals
    for ($i = 0; $i < 3; $i++) {
        if ($board[$i][0] == $player && $board[$i][1] == $player && $board[$i][2] == $player) {
            return true; // Row win
        }
        if ($board[0][$i] == $player && $board[1][$i] == $player && $board[2][$i] == $player) {
            return true; // Column win
        }
    }
    if ($board[0][0] == $player && $board[1][1] == $player && $board[2][2] == $player) {
        return true; // Diagonal win (top-left to bottom-right)
    }
    if ($board[0][2] == $player && $board[1][1] == $player && $board[2][0] == $player) {
        return true; // Diagonal win (top-right to bottom-left)
    }
    return false; // No win
}

function isTiedGame($board)
{
    foreach ($board as $row) {
        if (in_array('', $row)) {
            return false;
        }
    }
    return true;
}

// Function to make an automated O move
function makeOMove($board)
{
    // Collect all empty cells
    $emptyCells = [];
    for ($i = 0; $i < 3; $i++) {
        for ($j = 0; $j < 3; $j++) {
            if ($board[$i][$j] == '') {
                $emptyCells[] = [$i, $j];
            }
        }
    }

    // If there are empty cells, choose a random one

    //FIXME: this function always comes after isTiedGame, and the function checks whether any row contains empty cells. So, I believe this condition is always true.
    if (!empty($emptyCells)) {
        $randomCell = $emptyCells[array_rand($emptyCells)];
        $board[$randomCell[0]][$randomCell[1]] = 'O';
    }

    return $board;
}

function displayBoard($board, $name)
{
    echo "<p>Hello $name, " . date('Y-m-d H:i:s') . "</p>";
    echo "<table border='1'>";
    // var_dump($board); echo '<br>';
    for ($i = 0; $i < 3; $i++) {
        echo "<tr>";
        for ($j = 0; $j < 3; $j++) {
            $cellValue = $board[$i][$j] == '' ? "<a href='/ttt.php?name=$name&board=" . urlencode(implode(' ', array_merge(...$board))) . "&move=$i$j'> - </a>" : $board[$i][$j];
            echo "<td>$cellValue</td>";
        }
        echo "</tr>";
    }
    echo "</table>";
}

if (isset($_GET['name'])) { // Check if 'name' parameter is set in the URL
    $name = htmlspecialchars($_GET['name']); // Sanitize input

    if (!isset($_GET['board'])) { // Initialize the board if not set
        $board = [['', '', ''], ['', '', ''], ['', '', '']];
    } else {
        // Decode and update the board with the move
        $boardValues = explode(' ', urldecode($_GET['board']));
        $board = array_chunk($boardValues, 3);

        //FIXME: there should be only two parameters: name and board
        if (isset($_GET['move']) && strlen($_GET['move']) == 2) {
            $moveRow = $_GET['move'][0];
            $moveCol = $_GET['move'][1];
            if ($board[$moveRow][$moveCol] == '') {
                $board[$moveRow][$moveCol] = 'X'; // Player X makes a move
            }
        }

        if (checkWinner($board, 'X')) {
            echo "<p>You won!</p>";
            displayBoard($board, $name);
            echo "<p><a href='/ttt.php?name=$name'>Play again</a></p>";
            exit();
        }

        //FIXME: I think there should be Play Again button
        if (isTiedGame($board)) {
            echo "<p>WINNER: NONE. A STRANGE GAME. THE ONLY WINNING MOVE IS NOT TO PLAY.</p>";
            displayBoard($board, $name);
            echo "<p><a href='/ttt.php?name=$name'>Play again</a></p>";
            exit();
        }

        $board = makeOMove($board);
        if (checkWinner($board, 'O')) {
            echo "<p>I won!</p>";
            displayBoard($board, $name);
            echo "<p><a href='/ttt.php?name=$name'>Play again</a></p>";
            exit();
        }
    }

    displayBoard($board, $name);
} else {
    echo '
    <form action="/ttt.php" method="GET">
        <label for="name">Enter your name:</label>
        <input type="text" id="name" name="name" required>
        <button type="submit">Submit</button>
    </form>';
}
?>

</body>
</html>
