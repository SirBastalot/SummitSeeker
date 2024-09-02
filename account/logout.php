<?php
session_start();
session_unset(); // Alle Session-Variablen löschen
session_destroy(); // Sitzung zerstören

header("Location: login.html"); // Weiterleitung zur Login-Seite
exit();
?>
