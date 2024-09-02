<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
</head>

<body>
    <h2>Register</h2>
    <form action="register.php" method="POST">
        <label for="username">Username:</label>
        <input type="text" id="username" name="username" required><br>

        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required><br>

        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required><br>

        <input type="submit" value="Register">
    </form>
</body>

</html>

<?php
// Verbindung zur Datenbank herstellen
$servername = "localhost";
$username = "root";
$password = "maRJN6D12bWB";
$dbname = "climbing_app";
$conn = new mysqli($servername, $username, $password, $dbname);

// Überprüfen, ob die Verbindung funktioniert
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Nutzereingaben abrufen
$username = $_POST['username'];
$email = $_POST['email'];
$password = $_POST['password'];

// Überprüfen, ob die E-Mail-Adresse bereits registriert ist
$sql = "SELECT id FROM users WHERE email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    echo "This email is already registered. Please use a different email.";
} else {
    // Passwort hashen
    $password_hash = password_hash($password, PASSWORD_BCRYPT);

    // Benutzer in die Datenbank einfügen
    $sql = "INSERT INTO users (username, email, password_hash) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sss", $username, $email, $password_hash);

    if ($stmt->execute()) {
        echo "Registration successful! You can now <a href='login.html'>log in</a>.";
    } else {
        echo "An Error occured";
    }
}

// Verbindung schließen
$stmt->close();
$conn->close();
?>
