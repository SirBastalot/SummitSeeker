<?php
session_start(); // Sitzung starten

// Verbindung zur Datenbank herstellen
$host = "localhost";
$username = "root";
$password = "maRJN6D12bWB";
$dbname = "climbing_app";

$conn = new mysqli($servername, $username, $password, $dbname);

// Überprüfen, ob die Verbindung funktioniert
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Nutzereingaben abrufen
$email = $_POST['email'];
$password = $_POST['password'];

// Benutzer in der Datenbank suchen
$sql = "SELECT id, password_hash FROM users WHERE email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    $stmt->bind_result($id, $password_hash);
    $stmt->fetch();

    // Passwort überprüfen
    if (password_verify($password, $password_hash)) {
        // Benutzer-ID in der Session speichern
        $_SESSION['user_id'] = $id;
        $_SESSION['email'] = $email;
        header("Location: account.php"); // Weiterleitung zur Kontoseite
        exit();
    } else {
        echo "Invalid email or password.";
    }
} else {
    echo "Invalid email or password.";
}

// Verbindung schließen
$stmt->close();
$conn->close();
?>
