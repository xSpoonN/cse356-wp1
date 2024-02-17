<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="base.css">
    <link rel="stylesheet" href="style-battleship.css">
    <title>Battleship Game</title>
</head>
<body>

<?php
function initializeBoard()
{
    // Define the dimensions of the game board
    $rows = 5;
    $cols = 7;
    
    // Initialize an empty board
    $board = array_fill(0, $rows, array_fill(0, $cols, ''));

    // Place each ship on the board
    for ($size=2; $size <= 4; $size++) {
        $placed = false;
        
        // Randomly select starting position and direction until ship is placed
        while (!$placed) {
            $startRow = rand(0, $rows - 1);
            $startCol = rand(0, $cols - 1);
            $direction = rand(0, 1); // 0 for horizontal, 1 for vertical
            
            // Check if ship fits within the board boundaries
            if (($direction == 0 && $startCol + $size <= $cols) || ($direction == 1 && $startRow + $size <= $rows)) {
                // Check if ship overlaps with existing ships
                $overlap = false;
                for ($i = 0; $i < $size; $i++) {
                    if ($direction == 0) {
                        if ($board[$startRow][$startCol + $i] !== '') {
                            $overlap = true;
                            break;
                        }
                    } else {
                        if ($board[$startRow + $i][$startCol] !== '') {
                            $overlap = true;
                            break;
                        }
                    }
                }
                
                // If no overlap, place the ship on the board
                if (!$overlap) {
                    for ($i = 0; $i < $size; $i++) {
                        if ($direction == 0) {
                            $board[$startRow][$startCol + $i] = 'H';
                        } else {
                            $board[$startRow + $i][$startCol] = 'H';
                        }
                    }
                    $placed = true;
                }
            }
        }
    }

    return $board;
}

//Display the board with the ships and the moves left
function displayBoard($board, $name, $movesLeft, $isGameOver)
{
    echo "<p>Hello {$name}, " . date('Y-m-d H:i:s') . "</p>";

    echo "<form action='battleship.php' method='POST'>";
    echo "<div class='result-bar'><span style='margin-left: auto'>Moves left: {$movesLeft}</span></div>";
    echo "<table border='1'>";
    for ($row = 0; $row < 5; $row++) {
        echo "<tr>";
        for ($col = 0; $col < 7; $col++) {
            $cellValue = $board[$row][$col] == '' || $board[$row][$col] == 'H' ? "<button class='cell' type='submit' name='move' value='{$row}{$col}'> ? </button>" : $board[$row][$col];
            echo "<td>" . ($isGameOver ? '' : $cellValue) . "</td>";
        }
        echo "</tr>";
    }
    echo "</table>";
    echo "</form>";
}

//Make a move on the by marking the result on the board
function makeMove($board, $row, $col) {
    if($board[$row][$col] === 'H'){
        $board[$row][$col] = 'X';
        $_SESSION["movesLeft"]--;
    }elseif($board[$row][$col] === ''){
        $board[$row][$col] = 'O';
        $_SESSION["movesLeft"]--;
    }

    return $board;
}

//Check if the game is over by checking 1) remaining moves and 2) if all the ships are sunk
// 0 -> NOT GAME OVER, 1 -> NO REMAINING MOVES (lose), 2 -> ALL SHIPS SUNK (win)
function isGameOver($board, $movesLeft){
    // Iterate over the board to check remaining ships
    $remainingShips = 0;
    foreach ($board as $row) {
        foreach ($row as $cell) {
            if ($cell === 'H') {
                $remainingShips++;
            }
        }
    }

    // If no moves are remaining and ships are still on the board, the game is lost
    if($movesLeft === 0 && $remainingShips > 0){
        return 1; // No moves left (lose)
    }

    // If no ships are remaining, the game is won
    if($remainingShips === 0){
        return 2; // All ships sunk (win)
    }

    return 0; // Not game over
}

// Shows the game when 1) new post request was received 2) session already exists
if (($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['name'])) || isset($_SESSION['board'])) {
    // When new post request was received, initialize the game
    if(!isset($_SESSION["board"]) || isset($_POST['play_again'])){
        $_SESSION["name"] = isset($_SESSION["name"]) ? $_SESSION["name"] : htmlspecialchars($_POST['name']);
        $_SESSION["board"] = initializeBoard();
        $_SESSION["movesLeft"] = intval(ceil(7 * 5 * 0.6));
    }
    // If the session exsists and move was submitted, make the move
    elseif (isset($_POST['move'])){
        $row = $_POST['move'][0];
        $col = $_POST['move'][1];
        $_SESSION["board"] = makeMove($_SESSION["board"], $row, $col);

        if($res = isGameOver($_SESSION["board"], $_SESSION["movesLeft"])){
            echo "<p>" . ($res === 1 ? 'You lose!' : 'You win!') . "</p>";
            displayBoard($_SESSION["board"], $_SESSION["name"], $_SESSION["movesLeft"], true);
            echo "<p><form action='/battleship.php' method='POST'>
                    <button type='submit' name='play_again'>Play again</button>
                  </form></p>";
            exit();
        }
    }

    displayBoard($_SESSION["board"], $_SESSION["name"], $_SESSION["movesLeft"], false);
}else {
    echo '
    <form action="/battleship.php" method="POST">
        <label for="name">Enter your name:</label>
        <input type="text" id="name" name="name" required>
        <button type="submit">Submit</button>
    </form>';
}
?>
</body>
</html>