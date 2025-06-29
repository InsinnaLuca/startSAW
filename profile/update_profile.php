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

// AGGIORNA PROFILO
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])) {
    $email = trim($_POST['email']);
    $firstname = trim($_POST['firstname']);
    $lastname = trim($_POST['lastname']);
    $city = isset($_POST['city']) ? trim($_POST['city']) : null;
    $about_me = isset($_POST['about_me']) ? trim($_POST['about_me']) : null;
    $website_url = isset($_POST['website_url']) ? trim($_POST['website_url']) : null;
    $social_links = isset($_POST['social_links']) ? trim($_POST['social_links']) : null;

    // Verifica formato email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "Formato email non valido.";
        exit;
    }

    // Verifica email già usata da altri
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
    $stmt->bind_param("si", $email, $user_id);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows > 0) {
        echo "Email già in uso.";
        $stmt->close();
        $conn->close();
        exit;
    }
    $stmt->close();

    // Aggiorna i dati
    $stmt = $conn->prepare("UPDATE users SET email = ?, first_name = ?, last_name = ?, city = ?, about_me = ?, website_url = ?, social_links = ? WHERE id = ?");
    $stmt->bind_param("sssssssi", $email, $firstname, $lastname, $city, $about_me, $website_url, $social_links, $user_id);

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

// MOSTRA FORM
$stmt = $conn->prepare("SELECT email, first_name, last_name, city, about_me, website_url, social_links FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($email, $first_name, $last_name, $city, $about_me, $website_url, $social_links);
$stmt->fetch();
$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="it">
<head>
  <meta charset="UTF-8">
  <title>Modifica Profilo</title>
  <link rel="stylesheet" href="../css/style.css">
</head>
<body>
  <h1>Modifica Profilo</h1>
  <form action="update_profile.php" method="post">
    <input type="text" name="firstname" value="<?= htmlspecialchars($first_name) ?>" required pattern="[A-Za-z\s]{2,}" title="Solo lettere e spazi."><br>
    <input type="text" name="lastname" value="<?= htmlspecialchars($last_name) ?>" required pattern="[A-Za-z\s]{2,}" title="Solo lettere e spazi."><br>
    <input type="email" name="email" value="<?= htmlspecialchars($email) ?>" required><br>

    <input type="text" name="city" placeholder="Città" value="<?= htmlspecialchars($city ?? '') ?>"><br>
    <textarea name="about_me" placeholder="About me" rows="4" cols="40"><?= htmlspecialchars($about_me ?? '') ?></textarea><br>
    <input type="url" name="website_url" placeholder="Sito personale" value="<?= htmlspecialchars($website_url ?? '') ?>"><br>
    <textarea name="social_links" placeholder="Social (uno per riga)" rows="2" cols="40"><?= htmlspecialchars($social_links ?? '') ?></textarea><br>

    <input type="submit" name="submit" value="Salva modifiche">
  </form>
  <p>
    <a href="../index.php">
      <button type="button">Torna alla Home</button>
    </a>
  </p>
</body>
</html>