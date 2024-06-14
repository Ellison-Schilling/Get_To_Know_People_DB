<!DOCTYPE html>
<html>
<head>
    <title>What About Them?</title>
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

        select, input[type="text"] {
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
        <h2>What About Them?</h2>
        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
            <label for="personName">Select Person:</label>
            <select name="personName" id="personName" required>
                <?php
                // Include the database connection details
                include('connectionDataGTK.txt');

                // Connect to the database
                $conn = mysqli_connect($server, $user, $pass, $dbname, $port) or die('Error connecting to MySQL server.');

                // Fetch the names of people from the database
                $query = "SELECT CONCAT(FirstName, ' ', LastName) AS FullName FROM People";
                $result = mysqli_query($conn, $query) or die(mysqli_error($conn));

                // Generate the options for the select dropdown
                while ($row = mysqli_fetch_assoc($result)) {
                    echo "<option value='" . htmlspecialchars($row['FullName']) . "'>" . htmlspecialchars($row['FullName']) . "</option>";
                }

                // Close the database connection
                mysqli_close($conn);
                ?>
            </select>
            <input type="submit" value="Find Details">
        </form>

        <?php
        // Include the database connection details
        include('connectionDataGTK.txt');

        // Connect to the database
        $conn = mysqli_connect($server, $user, $pass, $dbname, $port) or die('Error connecting to MySQL server.');

        // Check if the form is submitted
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            // Get the person's name from the form
            $personName = mysqli_real_escape_string($conn, $_POST['personName']);

            // SQL query to fetch the person's details (Birthday, Favorite Color, State)
            $query = "SELECT BirthDate, FavoriteColor, State
                      FROM People
                      WHERE CONCAT(FirstName, ' ', LastName) = '$personName'";

            // Execute the query
            $result = mysqli_query($conn, $query) or die(mysqli_error($conn));

            // Check if a result is returned
            if (mysqli_num_rows($result) > 0) {
                $row = mysqli_fetch_assoc($result);
                $birthday = $row['BirthDate'];
                $favoriteColor = $row['FavoriteColor'];
                $state = $row['State'];
                echo "<div class='result'>"
                     . "<h3>Person Information</h3>" . "</p>"
                     . "<strong>Name: </strong>" . htmlspecialchars($personName) . "</p>" 
                     . "<strong>Birthday: </strong>" . htmlspecialchars($birthday) . "</p>"
                     . "<strong>Favorite Color:</strong> " . htmlspecialchars($favoriteColor) . "</p>"
                     . "<strong>State:</strong> " . htmlspecialchars($state) . "</p>"
                     . "</div>";
            } else {
                echo "<div class='result'>No person found with the name $personName.</div>";
            }
        }

        // Close the database connection
        mysqli_close($conn);
        ?>

        <a href="GTKindex.html">Return to Home</a>
    </div>
</body>
</html>
