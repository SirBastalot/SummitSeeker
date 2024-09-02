<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Routendetails - Summit Seeker</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        /* Allgemeine Stile */
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            color: #333;
        }
        
        /* Hintergrundvideo */
        #background-video {
            position: fixed;
            right: 0;
            bottom: 0;
            min-width: 100%;
            min-height: 100%;
            width: auto;
            height: auto;
            z-index: -1;
        }
        
        /* Header-Bereich */
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
            position: relative;
        }
        
        /* Hauptbereich */
        .container {
            text-align: center;
            padding: 30px;
        }
        
        .container h2 {
            font-size: 32px;
            margin: 20px 0;
            text-align: left;
            color: #FF5733;
        }
        

        .route-detail {
            border: 1px solid #ccc;
            border-radius: 5px;
            padding: 20px;
            background-color: #fff;
            width: 800px; /* Feste Breite statt 100% */
            margin: 20px auto;
            text-align: left;
        }

        
        .route-detail h3 {
            margin-top: 0;
        }

        #map {
            height: 450px;
            width: 840px; /* Gleiche Breite wie .route-detail */
            margin: 20px auto;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
    </style>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.3/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.3/dist/leaflet.js"></script>
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
            <button onclick="window.location.href='login.html'">Dein Konto</button>
        </nav>
    </header>

    <div class="container">
        <h2>Routendetails</h2>
        <?php

        function convertCoordinates($coordString) {
            $parts = explode(' ', $coordString);

            $lat_degrees = substr($parts[0], 1); //entfernt N
            $lat_minutes = $parts[1];
            $latitude = $lat_degrees + ($lat_minutes / 60);

            if ($parts[0][0] === 'S') { // Süden
                $latitude = -$latitude;
            }

            $lng_degrees = substr($parts[2], 1); //entfernt E
            $lng_minutes = $parts[3];
            $longitude = $lng_degrees + ($lng_minutes / 60);

            if ($parts[2][0] === 'W') { // Westen
                $longitude = -$longitude;
            }

            return [$latitude, $longitude];
        }

        if (isset($_GET['id'])) {
            $host = "localhost";
            $username = "root";
            $password = "maRJN6D12bWB";
            $dbname = "climbingroutes_db";
            $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
            $routen_id = $_GET['id'];
            $sqlQuery = "SELECT Routen.routen_name, Routen.schwierigkeit, Routen.routenlaenge, Routen.routen_beschreibung, 
                                Sektoren.sektor_name, Sektoren.koordinaten, Sektoren.untergebiets_id, UnterGebiete.untergebiets_name, 
                                UnterGebiete.park_koordinaten, UnterGebiete.fels_koordinaten, UnterGebiete.hoehenmeter, 
                                UnterGebiete.wand_ausrichtung, UnterGebiete.schoenheit, UnterGebiete.sicherheit_bewertung, 
                                UnterGebiete.besucherandrang, UnterGebiete.komfort, UnterGebiete.parkplatz, 
                                UnterGebiete.anfaengerfreundlich, UnterGebiete.regensicher, UnterGebiete.familienfreundlich, 
                                Gebiete.gebiets_name 
                         FROM Routen 
                         JOIN Sektoren ON Routen.sektor_id = Sektoren.sektor_id
                         JOIN UnterGebiete ON Sektoren.untergebiets_id = UnterGebiete.untergebiets_id
                         JOIN Gebiete ON UnterGebiete.gebiets_referenz = Gebiete.gebiets_id
                         WHERE Routen.routen_id = $routen_id";
            
            $answer = $pdo->query($sqlQuery);
            if($answer) {
                foreach($answer as $row) {
                    echo "<div class='route-container'>";
                    
                    // Routen Details
                    echo "<div class='route-detail'>";
                    echo "<h2>Routeninformationen</h2>";
                    echo "<h3>" . htmlspecialchars($row["routen_name"]) . "</h3>";
                    echo "<p><strong>Schwierigkeit:</strong> " . htmlspecialchars($row["schwierigkeit"]) . "</p>";
                    echo "<p><strong>Länge:</strong> " . htmlspecialchars($row["routenlaenge"]) . " m</p>";
                    echo "<p><strong>Beschreibung:</strong> " . htmlspecialchars($row["routen_beschreibung"]) . "</p>";
                    $sektorName = htmlspecialchars($row["sektor_name"]);
                    if (strpos($sektorName, "Sektor") !== false) {
                        // Entfernen des Texts "Sektor" und alle Zeichen davor
                        $sektorName = preg_replace('/^.*Sektor\s*/', '', $sektorName); #copy
                    }
                    echo "<p><strong>Sektor:</strong> " . $sektorName . "</p>";                    echo "</div>";
        
                    // Klettergebiet-Details
                    echo "<div class='route-detail'>";
                    echo "<h2>Gebietsinformationen</h2>";
                    echo "<p><strong>Untergebiet:</strong> " . htmlspecialchars($row["untergebiets_name"]) . "</p>";
                    echo "<p><strong>Gebiet:</strong> " . htmlspecialchars($row["gebiets_name"]) . "</p>";
                    echo "<p><strong>Höhenmeter:</strong> " . htmlspecialchars($row["hoehenmeter"]) . " m</p>";
                    echo "<p><strong>Wand Ausrichtung:</strong> " . htmlspecialchars($row["wand_ausrichtung"]) . "</p>";
                    echo "<p><strong>Schönheit:</strong> " . htmlspecialchars($row["schoenheit"]) . "</p>";
                    echo "<p><strong>Sicherheitsbewertung:</strong> " . htmlspecialchars($row["sicherheit_bewertung"]) . "</p>";
                    echo "<p><strong>Besucherandrang:</strong> " . htmlspecialchars($row["besucherandrang"]) . "</p>";
                    echo "<p><strong>Komfort:</strong> " . htmlspecialchars($row["komfort"]) . "</p>";
                    echo "<p><strong>Parkplatz:</strong> " . htmlspecialchars($row["parkplatz"]) . "</p>";
                    echo "<p><strong>Anfängerfreundlich:</strong> " . ($row["anfaengerfreundlich"] ? "Ja" : "Nein") . "</p>";
                    echo "<p><strong>Regensicher:</strong> " . ($row["regensicher"] ? "Ja" : "Nein") . "</p>";
                    echo "<p><strong>Familienfreundlich:</strong> " . ($row["familienfreundlich"] ? "Ja" : "Nein") . "</p>";
                    echo "</div>";
        
                    if (!empty($row["koordinaten"]) || !empty($row["park_koordinaten"])) {
                        list($sektor_lat, $sektor_lng) = convertCoordinates($row["koordinaten"]);
                        list($park_lat, $park_lng) = !empty($row["park_koordinaten"]) ? convertCoordinates($row["park_koordinaten"]) : [null, null];
        
                        echo "<div id='map'></div>";
        
                        echo "
                            <script>
                                var map = L.map('map').setView([$sektor_lat, $sektor_lng], 15);
                                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                                    maxZoom: 19,
                                    attribution: '© OpenStreetMap'
                                }).addTo(map);
                                L.marker([$sektor_lat, $sektor_lng]).addTo(map)
                                    .bindPopup('<b>" . htmlspecialchars($row["sektor_name"]) . "</b><br />" . htmlspecialchars($row["untergebiets_name"]) . "')
                                    .openPopup();
                                " . 
                                (!empty($row["park_koordinaten"]) ? "
                                L.marker([$park_lat, $park_lng], {color: 'red'}).addTo(map)
                                    .bindPopup('<b>Parkplatz</b><br />" . htmlspecialchars($row["untergebiets_name"]) . "')
                                    .openPopup();
                                " : "") . "
                            </script>
                        ";
                    } else {
                        echo "<p>Keine Koordinaten für diesen Sektor oder Parkplatz gefunden.</p>";
                    }
                    echo "</div>";
                }
            }
        }
        
        
        ?>
    </div>
</body>

</html>
