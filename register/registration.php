<?php
session_start();
require_once __DIR__ . '/../db/mysql_credentials.php';

$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if ($conn->connect_error) die("ERROR: Connessione fallita.");

// Se il form è stato inviato via POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])) {
    $email = trim($_POST['email']);
    $firstname = trim($_POST['firstname']);
    $lastname = trim($_POST['lastname']);
    $password = $_POST['pass'];
    $confirm = $_POST['confirm'];

    // Controllo campi obbligatori
    if ($email === '' || $firstname === '' || $lastname === '' || $password === '') {
        echo "ERROR: Tutti i campi sono obbligatori.";
        exit;
    }

    // Controllo formato email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "ERROR: Formato email non valido.";
        exit;
    }

    // Controllo corrispondenza password
    if ($password !== $confirm) {
        echo "ERROR: Le password non coincidono.";
        exit;
    }

    // Verifica se email già esistente
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows > 0) {
        echo "ERROR: Email già registrata.";
        $stmt->close();
        $conn->close();
        exit;
    }
    $stmt->close();

    // Hash password e inserimento
    $password_hash = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $conn->prepare("INSERT INTO users (email, password_hash, first_name, last_name) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $email, $password_hash, $firstname, $lastname);

    if ($stmt->execute()) {
        $_SESSION['email'] = $email;
        $_SESSION['first_name'] = $firstname;
        echo "SUCCESS: Registrazione completata.";
    } else {
        echo "ERROR: Errore nella registrazione.";
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
  <title>Registrazione</title>
  <link rel="stylesheet" href="../css/style.css">
</head>
<body>
  <h1>Registrati</h1>
  <form method="post">
    <label>Email: <input type="email" name="email" required></label><br>
    <label>Nome: <input type="text" name="firstname" required></label><br>
    <label>Cognome: <input type="text" name="lastname" required></label><br>
    <label>Password: <input type="password" name="pass" required></label><br>
    <label>Conferma Password: <input type="password" name="confirm" required></label><br>
    <button type="submit" name="submit" value="submit">Registrati</button>
  </form>
  <p>
    <a href="../index.php">
      <button type="button">Torna alla Home</button>
    </a>
  </p>
</body>
</html>
