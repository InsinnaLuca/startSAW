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

$stmt = $conn->prepare("SELECT email, first_name, last_name, city, about_me, website_url, social_links FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($email, $first_name, $last_name, $city, $about_me, $website_url, $social_links);
$stmt->fetch();
$stmt->close();
$conn->close();

// Se il test richiede output raw (test automatici)
if (isset($_GET['raw'])) {
    echo "EMAIL: $email\n";
    echo "FIRST: $first_name\n";
    echo "LAST: $last_name\n";
    exit;
}
?>

<!DOCTYPE html>
<html lang="it">
<head>
  <meta charset="UTF-8">
  <title>Il mio profilo</title>
  <link rel="stylesheet" href="../css/style.css">
</head>
<body>
  <h1>Il mio profilo</h1>
  <p>Email: <strong><?= htmlspecialchars($email) ?></strong></p>
  <p>Nome: <strong><?= htmlspecialchars($first_name) ?></strong></p>
  <p>Cognome: <strong><?= htmlspecialchars($last_name) ?></strong></p>
  <?php if ($city): ?><p>Città: <?= htmlspecialchars($city) ?></p><?php endif; ?>
  <?php if ($about_me): ?><p>About me: <?= nl2br(htmlspecialchars($about_me)) ?></p><?php endif; ?>
  <?php if ($website_url): ?><p>Website: <a href="<?= htmlspecialchars($website_url) ?>" target="_blank"><?= htmlspecialchars($website_url) ?></a></p><?php endif; ?>
  <?php if ($social_links): ?><p>Social: <?= nl2br(htmlspecialchars($social_links)) ?></p><?php endif; ?>
  <p>
    <a href="update_profile.php"><button>Modifica Profilo</button></a>
  </p>
  <p>
    <a href="../index.php"><button type="button">Torna alla Home</button></a>
  </p>
</body>
</html>