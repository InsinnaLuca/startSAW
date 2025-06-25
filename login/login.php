<?php
session_start();
require_once __DIR__ . '/../db/mysql_credentials.php';

$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if ($conn->connect_error) die("Connessione fallita: " . $conn->connect_error);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])) {
    $email = trim($_POST['email']);
    $password = $_POST['pass'];

    // Verifica se l'utente esiste
    $stmt = $conn->prepare("SELECT id, password_hash, first_name FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows === 1) {
        $stmt->bind_result($id, $hash, $first_name);
        $stmt->fetch();

        if (password_verify($password, $hash)) {
            $_SESSION['user_id'] = $id;
            $_SESSION['email'] = $email;
            $_SESSION['first_name'] = $first_name;

            echo "Login avvenuto";
        } else {
            echo "Credenziali errate";
        }
    } else {
        echo "Utente non trovato";
    }

    $stmt->close();
    $conn->close();
    exit;
}
?>

<!-- Form HTML -->
<!DOCTYPE html>
<html lang="it">
<head>
  <meta charset="UTF-8">
  <title>Login</title>
</head>
<body>
  <h1>Login</h1>
  <form method="post">
    <label>Email: <input type="email" name="email" required></label><br>
    <label>Password: <input type="password" name="pass" required></label><br>
    <button type="submit" name="submit" value="submit">Login</button>
  </form>
</body>
</html>