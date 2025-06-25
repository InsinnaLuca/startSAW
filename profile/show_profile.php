<?php
session_start();
require_once __DIR__ . '/../db/mysql_credentials.php';

if (!isset($_SESSION['user_id'])) {
    echo "Accesso negato";
    exit;
}

$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if ($conn->connect_error) die("Connessione fallita: " . $conn->connect_error);

$user_id = $_SESSION['user_id'];

$stmt = $conn->prepare("SELECT email, first_name, last_name FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($email, $first_name, $last_name);
$stmt->fetch();
$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="it">
<head>
  <meta charset="UTF-8">
  <title>Il mio profilo</title>
</head>
<body>
  <h1>Profilo utente</h1>
  <p>Email: <strong><?= htmlspecialchars($email) ?></strong></p>
  <p>Nome: <strong><?= htmlspecialchars($first_name) ?></strong></p>
  <p>Cognome: <strong><?= htmlspecialchars($last_name) ?></strong></p>
</body>
</html>