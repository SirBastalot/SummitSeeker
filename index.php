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
    $answer = $pdo->query($sqlQuery);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Summit Seeker</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        /* Die gleichen Stile wie in index.html, können hier kopiert werden */
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
                <!-- Die Optionen hier bleiben gleich -->
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
        if (isset($answer)) {
            foreach ($answer as $row) {
                echo "<a href='route_details.php?id=" . $row['routen_id'] . "' class='route-preview'>";
                echo "<h3>" . $row["routen_name"] . "</h3>";
                echo "<p>" . $row["routen_beschreibung"] . "</p>";
                echo "</a>";
            }
        }
        ?>
    </div>
</body>

</html>
