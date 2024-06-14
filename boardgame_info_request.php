<!DOCTYPE html>
<html>
<head>
    <title>Board Game Information</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f2f2f2;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #ffffff;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            border-radius: 5px;
        }

        h2 {
            text-align: center;
            color: #333333;
        }

        form {
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        label {
            font-weight: bold;
            margin-bottom: 5px;
        }

        select {
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 4px;
            margin-bottom: 10px;
            width: 300px;
        }

        input[type="submit"] {
            background-color: #4CAF50;
            color: #ffffff;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        input[type="submit"]:hover {
            background-color: #45a049;
        }

        .result {
            text-align: center;
            margin-top: 20px;
        }

        .result h3 {
            color: #333333;
        }

        .result p {
            font-weight: normal;
            margin: 5px 0;
        }

        a {
            display: block;
            text-align: center;
            margin-top: 20px;
            color: #4CAF50;
            text-decoration: none;
        }

        a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Board Game Information</h2>
        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
            <label for="boardGame">Select a Board Game:</label>
            <select name="boardGame" id="boardGame" required>
                <?php
                // Include the database connection details
                include('connectionDataGTK.txt');

                // Connect to the database
                $conn = mysqli_connect($server, $user, $pass, $dbname, $port) or die('Error connecting to MySQL server.');

                // SQL query to fetch all board games
                $query = "SELECT BoardGameID, GameName FROM BoardGames";
                $result = mysqli_query($conn, $query) or die(mysqli_error($conn));

                // Generate the options for the select dropdown
                while ($row = mysqli_fetch_assoc($result)) {
                    echo "<option value='" . $row['BoardGameID'] . "'>" . $row['GameName'] . "</option>";
                }

                // Close the database connection
                mysqli_close($conn);
                ?>
            </select>
            <input type="submit" value="Get Information">
        </form>

        <?php
        // Include the database connection details
        include('connectionDataGTK.txt');

        // Connect to the database
        $conn = mysqli_connect($server, $user, $pass, $dbname, $port) or die('Error connecting to MySQL server.');

        // Check if the form is submitted
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            // Get the selected board game ID from the form
            $boardGameID = mysqli_real_escape_string($conn, $_POST['boardGame']);

            // SQL query to fetch the board game information including duration
            $query = "SELECT GameName, MinPlayers, MaxPlayers, play_duration
                      FROM BoardGames
                      WHERE BoardGameID = $boardGameID";
            $result = mysqli_query($conn, $query) or die(mysqli_error($conn));

            // Check if a result is returned
            if (mysqli_num_rows($result) > 0) {
                $row = mysqli_fetch_assoc($result);
                echo "<div class='result'>
                        <h3>Board Game Information</h3>
                        <p><strong>Game Name:</strong> " . $row['GameName'] . "</p>
                        <p><strong>Minimum Players:</strong> " . $row['MinPlayers'] . "</p>
                        <p><strong>Maximum Players:</strong> " . $row['MaxPlayers'] . "</p>
                        <p><strong>Duration:</strong> " . $row['play_duration'] . " minutes</p>
                      </div>";
            } else {
                echo "<div class='result'>No information found for the selected board game.</div>";
            }
        }

        // Close the database connection
        mysqli_close($conn);
        ?>
        <a href="GTKindex.html">Return to Home</a>
    </div>
</body>
</html>
