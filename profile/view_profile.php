<?php
require_once __DIR__ . '/../db/mysql_credentials.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo "Profilo non valido.";
    exit;
}

$user_id = (int)$_GET['id'];

$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if ($conn->connect_error) {
    die("Connessione fallita: " . $conn->connect_error);
}

$stmt = $conn->prepare("SELECT email, first_name, last_name, city, about_me, website_url, social_links FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($email, $first_name, $last_name, $city, $about_me, $website_url, $social_links);
if (!$stmt->fetch()) {
    echo "Utente non trovato.";
    exit;
}
$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="it">
<head>
  <meta charset="UTF-8">
  <title>Profilo utente</title>
  <link rel="stylesheet" href="../css/style.css">
</head>
<body>
  <?php include '../includes/header.php'; ?>

  <main class="container">
    <h1>Profilo pubblico</h1>
    <p>Nome: <strong><?= htmlspecialchars($first_name) ?> <?= htmlspecialchars($last_name) ?></strong></p>
    <?php if ($city): ?><p>Città: <?= htmlspecialchars($city) ?></p><?php endif; ?>
    <?php if ($about_me): ?><p>About me: <?= nl2br(htmlspecialchars($about_me)) ?></p><?php endif; ?>
    <?php if ($website_url): ?><p>Website: <a href="<?= htmlspecialchars($website_url) ?>" target="_blank"><?= htmlspecialchars($website_url) ?></a></p><?php endif; ?>
    <?php if ($social_links): ?><p>Social: <?= nl2br(htmlspecialchars($social_links)) ?></p><?php endif; ?>
    <p><a href="../index.php"><button type="button">Torna alla Home</button></a></p>
  </main>
</body>
</html>
