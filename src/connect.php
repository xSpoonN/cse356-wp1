<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connect-4 Game</title>
    <link rel="stylesheet" href="base.css">
    <link rel="stylesheet" href="style-c4.css">
</head>
<body>

<?php
function checkWinner($board, $player)
{
    // Check horizontally
    for ($row = 0; $row < 5; $row++) {
        for ($col = 0; $col < 4; $col++) {
            if ($board[$row][$col] == $player &&
                $board[$row][$col + 1] == $player &&
                $board[$row][$col + 2] == $player &&
                $board[$row][$col + 3] == $player) {
                return true;
            }
        }
    }

    // Check vertically
    for ($row = 0; $row < 2; $row++) {
        for ($col = 0; $col < 7; $col++) {
            if ($board[$row][$col] == $player &&
                $board[$row + 1][$col] == $player &&
                $board[$row + 2][$col] == $player &&
                $board[$row + 3][$col] == $player) {
                return true;
            }
        }
    }

    // Check diagonally (down-right)
    for ($row = 0; $row < 2; $row++) {
        for ($col = 0; $col < 4; $col++) {
            if ($board[$row][$col] == $player &&
                $board[$row + 1][$col + 1] == $player &&
                $board[$row + 2][$col + 2] == $player &&
                $board[$row + 3][$col + 3] == $player) {
                return true;
            }
        }
    }

    // Check diagonally (up-right)
    for ($row = 3; $row < 5; $row++) {
        for ($col = 0; $col < 4; $col++) {
            if ($board[$row][$col] == $player &&
                $board[$row - 1][$col + 1] == $player &&
                $board[$row - 2][$col + 2] == $player &&
                $board[$row - 3][$col + 3] == $player) {
                return true;
            }
        }
    }

    return false;
}

function isDraw($board)
{
    foreach ($board as $row) {
        if (in_array('', $row)) {
            return false;
        }
    }
    return true;
}

function makeMove($board, $column, $player)
{
    for ($row = 4; $row >= 0; $row--) {
        if ($board[$row][$column] == '') {
            $board[$row][$column] = $player;
            return $board;
        }
    }
    return $board; // No available spot in the column
}

function makeServerMove($board) {
    // Collect all non-full columns
    $nonFullColumns = [];
    for ($col = 0; $col < 7; $col++) {
        if ($board[0][$col] == '') {
            $nonFullColumns[] = $col;
        }
    }

    if (!empty($nonFullColumns)) {
        $randomColumn = $nonFullColumns[array_rand($nonFullColumns)];
        $board = makeMove($board, $randomColumn, 'O');
    }

    return $board;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['name'])) {
    $name = htmlspecialchars($_POST['name']); // Sanitize input

    // Initialize the board if not set
    if (!isset($_POST['board'])) {
        $board = [['', '', '', '', '', '', ''], ['', '', '', '', '', '', ''], ['', '', '', '', '', '', ''], ['', '', '', '', '', '', ''], ['', '', '', '', '', '', '']];
    } else {
        // Split into rows using '.' as the delimiter, then split each row into columns using ' ' as the delimiter
        $board = array_map(function ($row) {
            return explode(' ', $row);
        }, explode('.', urldecode($_POST['board'])));

        if (isset($_POST['column']) && is_numeric($_POST['column'])) {
            $column = intval($_POST['column']);

            $board = makeMove($board, $column, 'X');
            if (checkWinner($board, 'X')) {
                echo "<p>You won!</p>";
                displayBoard($board, $name);
                echo "<p><form action='/connect.php' method='POST'>
                        <input type='hidden' name='name' value='$name'>
                        <button type='submit' name='play_again'>Play again</button>
                      </form></p>";
                exit();
            }
            if (isDraw($board)) {
                echo "<p>Draw!</p>";
                displayBoard($board, $name);
                echo "<p><form action='/connect.php' method='POST'>
                        <input type='hidden' name='name' value='$name'>
                        <button type='submit' name='play_again'>Play again</button>
                      </form></p>";
                exit();
            }
            $board = makeServerMove($board);
            if (checkWinner($board, 'O')) {
                echo "<p>I won!</p>";
                displayBoard($board, $name);
                echo "<p><form action='/connect.php' method='POST'>
                        <input type='hidden' name='name' value='$name'>
                        <button type='submit' name='play_again'>Play again</button>
                      </form></p>";
                exit();
            }

            // Check if the game is a draw after the server's move
            if (isDraw($board)) {
                echo "<p>Draw!</p>";
                displayBoard($board, $name);
                echo "<p><form action='/connect.php' method='POST'>
                        <input type='hidden' name='name' value='$name'>
                        <button type='submit' name='play_again'>Play again</button>
                      </form></p>";
                exit();
            }
        }
    }

    displayBoard($board, $name);
} else {
    echo '
    <form action="/connect.php" method="POST">
        <label for="name">Enter your name:</label>
        <input type="text" id="name" name="name" required>
        <button type="submit">Submit</button>
    </form>';
}

function displayBoard($board, $name)
{
    echo "<p>Hello $name, " . date('Y-m-d H:i:s') . "</p>";
    echo "<form action='/connect.php' method='POST'>";
    for ($col = 0; $col < 7; $col++) {
        // Check if the column is full
        $columnFull = ($board[0][$col] !== '');

        echo "<button type='submit' name='column' value='$col' class='colButton' " . ($columnFull ? 'disabled' : '') . ">" . ($col + 1) . "</button>";
    }
    echo "<input type='hidden' name='name' value='$name'>";
    $tmp = array_map(function($row) {
        return implode(' ', $row);
    }, $board);
    
    echo "<input type='hidden' name='board' value='" . urlencode(implode('.', $tmp)) . "'>";
    echo "</form>";

    echo "<table border='1'>";
    for ($row = 0; $row < 5; $row++) {
        echo "<tr>";
        for ($col = 0; $col < 7; $col++) {
            echo "<td>{$board[$row][$col]}</td>";
        }
        echo "</tr>";
    }
    echo "</table>";
}
?>

</body>
</html>
