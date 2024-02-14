<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="style.css">
        <title>TTT Page</title>
    </head>
    <body>
        <?php
            // Function to check if a player has won
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

            // Function to display the Tic-Tac-Toe board
            function displayBoard($board, $name)
            {
                echo "<p>Hello $name, " . date('Y-m-d H:i:s') . "</p>";
                echo "<table border='1'>";
                for ($i = 0; $i < 3; $i++) {
                    echo "<tr>";
                    for ($j = 0; $j < 3; $j++) {
                        $cellValue = $board[$i][$j] == '' ? "<a href='/ttt.php?name=$name&board=" . urlencode(implode(' ', array_merge(...$board))) . "&move=$i$j'> </a>" : $board[$i][$j];
                        echo "<td>$cellValue</td>";
                    }
                    echo "</tr>";
                }
                echo "</table>";
            }

            // Check if 'name' parameter is set in the URL
            if (isset($_GET['name'])) {
                $name = htmlspecialchars($_GET['name']); // Sanitize input

                // Initialize the board if not set
                if (!isset($_GET['board'])) {
                    $board = [['', '', ''], ['', '', ''], ['', '', '']];
                } else {
                    // Decode and update the board with the move
                    $board = array_chunk(str_split(urldecode($_GET['board'])), 3);
                    if (isset($_GET['move']) && strlen($_GET['move']) == 2) {
                        $moveRow = $_GET['move'][0];
                        $moveCol = $_GET['move'][1];
                        if ($board[$moveRow][$moveCol] == '') {
                            $board[$moveRow][$moveCol] = 'X'; // Player X makes a move
                        }
                    }

                    // Check if there is a winner
                    if (checkWinner($board, 'X')) {
                        echo "<p>Congratulations, you won!</p>";
                        displayBoard($board, $name);
                        exit();
                    }
                }

                // Display the Tic-Tac-Toe board
                displayBoard($board, $name);
            } else {
                // Display the form
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