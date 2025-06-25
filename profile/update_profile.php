<?php
session_start();
require_once __DIR__ . '/../db/mysql_credentials.php';

if (!isset($_SESSION['user_id'])) {
    echo "Accesso negato";
    exit;
}

$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if ($conn->connect_error) die("Connessione fallita: " . $conn->connect_error);

// Gestione POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])) {
    $email = trim($_POST['email']);
    $firstname = trim($_POST['firstname']);
    $lastname = trim($_POST['lastname']);

    if ($email === '' || $firstname === '' || $lastname === '') {
        echo "Tutti i campi sono obbligatori.";
        exit;
    }

    $user_id = $_SESSION['user_id'];

    // Controlla se l’email è già usata da un altro utente
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
    $stmt->bind_param("si", $email, $user_id);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows > 0) {
        echo "Email già in uso da un altro utente.";
        exit;
    }
    $stmt->close();

    // Aggiorna utente
    $stmt = $conn->prepare("UPDATE users SET email = ?, first_name = ?, last_name = ? WHERE id = ?");
    $stmt->bind_param("sssi", $email, $firstname, $lastname, $user_id);

    if ($stmt->execute()) {
        $_SESSION['email'] = $email;
        $_SESSION['first_name'] = $firstname;
        echo "Profilo aggiornato con successo";
    } else {
        echo "Errore durante l'aggiornamento";
    }

    $stmt->close();
    $conn->close();
    exit;
}
?>

<!-- HTML -->
<!DOCTYPE html>
<html lang="it">
<head>
  <meta charset="UTF-8">
  <title>Modifica Profilo</title>
</head>
<body>
  <h1>Modifica Profilo</h1>
  <form method="post">
    <label>Email: <input type="email" name="email" value="<?= htmlspecialchars($_SESSION['email']) ?>" required></label><br>
    <label>Nome: <input type="text" name="firstname" value="<?= htmlspecialchars($_SESSION['first_name']) ?>" required></label><br>
    <label>Cognome: <input type="text" name="lastname" required></label><br>
    <button type="submit" name="submit" value="submit">Aggiorna</button>
  </form>
</body>
</html>
