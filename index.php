<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Summit Seeker</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        /* Allgemeine Stile */
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            color: #333;
        }

        #background-video {
            position: fixed;
            right: 0;
            bottom: 0;
            z-index: -1; /* auf hinterster Ebene */
        }

        header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 5px;
            background: #f2f2f2a1;
        }

        h1 {
            font-size: 23px;
            margin: 0;
        }

        nav button {
            padding: 8px 15px;
            background-color: transparent;
            border: none;   
            font-size: 18px;
            cursor: pointer;
        }

        div {
            text-align: center;
            padding: 30px;
        }

        div h2 {
            font-size: 32px;
            margin: 20px 0;
            text-align: left;
            color: #FF5733;
        }

        .search-bar {
            justify-content: center;
            margin-top: 20px;
        }

        .search-bar input[type="text"] {
            padding: 8px;
            width: 300px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        .search-bar input[type="submit"] {
            padding: 8px 20px;
            margin-left: 10px;
            background-color: #FF5733;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .route-preview {
            margin-top: 20px;
            border: 1px solid #ccc;
            border-radius: 5px;
            padding: 10px;
            cursor: pointer;
            text-decoration: none;
            color: inherit;
            display: block;
            width: 80%;
            max-width: 600px;
            margin-left: auto;
            margin-right: auto;
            background-color: #fff;
        }

        .route-preview:hover {
            background-color: #f2f2f2;
        }

        .route-preview h3 {
            margin: 0;
            color: #333;
        }

        .route-preview p {
            margin: 5px 0;
        }
    </style>
</head>

<body>
    <video autoplay muted loop id="background-video">
        <source src="assets/background-video.mp4" type="video/mp4">
    </video>

    <header>
        <h1>Summit Seeker</h1>
        <nav>
            <button onclick="window.location.href='index.php'">Home</button>
            <button>Über uns</button>
            <button>Leistungen</button>
            <button>Kontakt</button>
            <button onclick="window.location.href='account/account.php'">Dein Konto</button>
        </nav>
    </header>

    <div>
        <h2>
            <span>Entdecke deine nächste <br>Herausforderung mit</span>
            <span>Summit Seeker</span>
        </h2>
    </div>

    <div class="filter-bar">
        <form method="post" action="">
            <select name="schwierigkeitsgrad">
                <option value="">Alle Schwierigkeitsgrade</option>
                <option value="1-3">1-3</option>
                <option value="4a">4a</option>
                <option value="4b">4b</option>
                <option value="4c">4c</option>
                <option value="5a">5a</option>
                <option value="5b">5b</option>
                <option value="5c">5c</option>
                <option value="6a">6a</option>
                <option value="6a+">6a+</option>
                <option value="6b">6b</option>
                <option value="6b+">6b+</option>
                <option value="6c">6c</option>
                <option value="6c+">6c+</option>
                <option value="7a">7a</option>
                <option value="7a+">7a+</option>
                <option value="7b">7b</option>
                <option value="7b+">7b+</option>
                <option value="7c">7c</option>
                <option value="7c+">7c+</option>
                <option value="8a">8a</option>
                <option value="8a+">8a+</option>
                <option value="8b">8b</option>
                <option value="8b+">8b+</option>
                <option value="8c">8c</option>
                <option value="8c+">8c+</option>
                <option value="9a">9a</option>
                <option value="9a+">9a+</option>
                <option value="9b">9b</option>
                <option value="9b+">9b+</option>
                <option value="9c">9c</option>
            </select>
            <select name="anfängerfreundlich">
                <option value="">Anfängerfreundlich</option>
                <option value="Ja">Ja</option>
                <option value="Nein">Nein</option>
            </select>
            <select name="regensicher">
                <option value="">Regensicher</option>
                <option value="Ja">Ja</option>
                <option value="Nein">Nein</option>
            </select>
            <select name="besucherandrang">
                <option value="">Besucherandrang</option>
                <option value="Sehr Stark">Sehr Stark</option>
                <option value="Stark">Stark</option>
                <option value="Mittel">Mittel</option>
                <option value="Schwach">Schwach</option>
            </select>
            <input type="submit" name="submit" value="Filtern">
        </form>
        <?php
        if (isset($_POST['submit'])) {
            $host = "localhost";
            $username = "root";
            $password = "maRJN6D12bWB";
            $dbname = "climbingroutes_db";
            
            $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);

           
            $schwierigkeitsgrad = $_POST['schwierigkeitsgrad'];
            $anfängerfreundlich = $_POST['anfängerfreundlich'];
            $regensicher = $_POST['regensicher'];
            $besucherandrang = $_POST['besucherandrang'];

            $sqlQuery = "SELECT * FROM Routen r
                JOIN Sektoren ON r.sektor_id = Sektoren.sektor_id
                JOIN UnterGebiete ON Sektoren.untergebiets_id = UnterGebiete.untergebiets_id
                JOIN Gebiete ON UnterGebiete.gebiets_referenz = Gebiete.gebiets_id";

            $conditions = [];

            if ($schwierigkeitsgrad) {
                $conditions[] = "r.schwierigkeit LIKE '$schwierigkeitsgrad%'";
            }

            if ($anfängerfreundlich) {
                $anfängerfreundlichValue = ($anfängerfreundlich === 'Ja') ? 1 : 0;
                $conditions[] = "UnterGebiete.anfaengerfreundlich = $anfängerfreundlichValue";
            }

            if ($regensicher) {
                $regensicherValue = ($regensicher === 'Ja') ? 1 : 0;
                $conditions[] = "UnterGebiete.regensicher = $regensicherValue";
            }

            if ($besucherandrang) {
                $conditions[] = "UnterGebiete.besucherandrang = '$besucherandrang'";
            }
            $conditions[] = "r.routen_beschreibung LIKE '%Fuß%'";  
            if (!empty($conditions)) {
                $sqlQuery .= " WHERE " . implode(' AND ', $conditions);
            }
            $answer = $pdo->query($sqlQuery); //false oder antwort + true...
            if($answer) {
                foreach($answer as $row) {
                    echo "<a href='route_details.php?id=" . $row['routen_id'] . "' class='route-preview'>";
                    echo "<h3>" . $row["routen_name"] . "</h3>";
                    echo "<p>" . $row["routen_beschreibung"] . "</p>";
                    echo "</a>";
                }
            }
        } 
        ?>
    </div>
</body>

</html>
