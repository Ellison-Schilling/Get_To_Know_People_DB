<!DOCTYPE html>
<html>
<head>
    <title>How Many People Can Play?</title>
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
        <h2>How Many People Can Play?</h2>
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
            // Get the board game ID from the form
            $boardGameID = mysqli_real_escape_string($conn, $_POST['boardGame']);

            // SQL query to fetch the names of players and their comfortability level
            $query = "SELECT CONCAT(p.FirstName, ' ', p.LastName) AS FullName, pb.comfortability_level
                    FROM People p
                    JOIN People_BoardGames pb ON p.PersonID = pb.PersonID
                    WHERE pb.BoardGameID = '$boardGameID'";

            // Execute the query
            $result = mysqli_query($conn, $query) or die(mysqli_error($conn));

            // Check if any results are returned
            if (mysqli_num_rows($result) > 0) {
                echo "<div class='result'>";
                echo "<h3>The number of people who can play the selected board game is " . mysqli_num_rows($result) . ".</h3>";
                echo "<strong>Players and Comfortability Levels:</strong><br>";

                // Loop through the results and display each player's name and comfortability level
                while ($row = mysqli_fetch_assoc($result)) {
                    echo "<p><strong>Name:</strong> " . $row['FullName'] . " | <strong>Comfortability Level:</strong> " . $row['comfortability_level'] . "</p>";
                }
                echo "</div>";
            } else {
                echo "<div class='result'>No players found for the selected board game.</div>";
            }
        }

        // Close the database connection
        mysqli_close($conn);
        ?>

        <br><br>
        <a href="GTKindex.html">Return to Home</a>
    </div>
</body>
</html>
