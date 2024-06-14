<!DOCTYPE html>
<html>
<head>
    <title>Find Out What Song to Play in a Group</title>
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

        input[type="text"] {
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 4px;
            margin-bottom: 10px;
            width: 300px;
        }

        select {
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 4px;
            margin-bottom: 10px;
            width: 300px;
        }

        button {
            background-color: #4CAF50;
            color: #ffffff;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.3s ease;
            margin-bottom: 10px;
        }

        button:hover {
            background-color: #45a049;
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

        .person-box {
            display: inline-block;
            background-color: #f2f2f2;
            padding: 5px 10px;
            border-radius: 4px;
            margin-right: 5px;
            margin-bottom: 5px;
        }

        .person-box .close {
            color: #888;
            font-weight: bold;
            margin-left: 5px;
            cursor: pointer;
        }

        .person-box .close:hover {
            color: #333;
        }
    </style>
    <script>
        var selectedPeople = [];

        function addPerson() {
            var personSelect = document.getElementById('personSelect');
            var selectedPerson = personSelect.options[personSelect.selectedIndex].value;
            if (selectedPerson) {
                var input = document.getElementById('peopleInput');
                var personBox = document.createElement('span');
                personBox.className = 'person-box';
                personBox.textContent = selectedPerson + ' ';
                var closeButton = document.createElement('span');
                closeButton.className = 'close';
                closeButton.textContent = 'x';
                closeButton.onclick = function() {
                    input.removeChild(personBox);
                    var option = document.createElement('option');
                    option.value = selectedPerson;
                    option.text = selectedPerson;
                    personSelect.add(option);
                    selectedPeople = selectedPeople.filter(function(person) {
                        return person !== selectedPerson;
                    });
                };
                personBox.appendChild(closeButton);
                input.appendChild(personBox);
                personSelect.remove(personSelect.selectedIndex);
                selectedPeople.push(selectedPerson);
            }
        }
    </script>
    <?php
    // Include the database connection details
    include('connectionDataGTK.txt');

    // Connect to the database
    $conn = mysqli_connect($server, $user, $pass, $dbname, $port) or die('Error connecting to MySQL server.');
    ?>
</head>
<body>
    <div class="container">
        <h2>Find Out What Song to Play in a Group</h2>
        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
            <label for="peopleInput">People:</label>
            <div id="peopleInput" contenteditable="false"></div>
            <br>
            <label for="personSelect">Add Person:</label>
            <select id="personSelect">
                <?php
                // Fetch people's names from the database
                $query = "SELECT CONCAT(FirstName, ' ', LastName) AS FullName FROM People";
                $result = mysqli_query($conn, $query) or die(mysqli_error($conn));

                // Populate the dropdown options
                while ($row = mysqli_fetch_assoc($result)) {
                    echo "<option value='" . $row['FullName'] . "'>" . $row['FullName'] . "</option>";
                }
                ?>
            </select>
            <button type="button" onclick="addPerson()">Add</button>
            <br>
            <input type="submit" value="Find Songs">
        </form>

        <?php
        // Check if the form is submitted
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            // Get the selected people from JavaScript
            $peopleArray = isset($_POST['selectedPeople']) ? explode(',', $_POST['selectedPeople']) : array();

            // Array to hold all songs liked by each person
            $songsPerPerson = [];

            // Fetch the songs liked by each person
            foreach ($peopleArray as $person) {
                $person = mysqli_real_escape_string($conn, $person);

                $query = "SELECT DISTINCT s.SongTitle, b.BandName
                        FROM Songs s
                        JOIN Bands b ON s.BandID = b.BandID
                        JOIN People_Bands pb ON b.BandID = pb.BandID
                        JOIN People p ON pb.PersonID = p.PersonID
                        WHERE CONCAT(p.FirstName, ' ', p.LastName) = '$person'";

                $result = mysqli_query($conn, $query) or die(mysqli_error($conn));

                $songs = [];
                while ($row = mysqli_fetch_assoc($result)) {
                    $songs[] = $row['SongTitle'] . " by " . $row['BandName'];
                }
                $songsPerPerson[] = $songs;
            }

            // Find the intersection of all song lists to get common songs
            $commonSongs = array_shift($songsPerPerson);
            foreach ($songsPerPerson as $songs) {
                $commonSongs = array_intersect($commonSongs, $songs);
            }

            // Display the results
            if (!empty($commonSongs)) {
                echo "<div class='result'><strong>Songs liked by all:<br></strong></div>";
                foreach ($commonSongs as $song) {
                    echo "<div class='result'>$song<br></div>";
                }
            } else {
                echo "<div class='result'>No common songs found for the given group.</div>";
            }
        }

        // Close the database connection
        mysqli_close($conn);
        ?>

        <a href="GTKindex.html">Return to Home</a>
    </div>
</body>
</html>