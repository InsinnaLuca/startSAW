<?php
session_start();
require_once __DIR__ . '/../db/mysql_credentials.php';

$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if ($conn->connect_error) die("Connessione fallita: " . $conn->connect_error);

// Recupera tutti i post
$sql = "SELECT id, title, created_at FROM blog_posts ORDER BY created_at DESC";
$result = $conn->query($sql);

// Recupera ruolo utente se loggato
$role = null;
if (isset($_SESSION['user_id'])) {
    $stmt = $conn->prepare("SELECT role FROM users WHERE id = ?");
    $stmt->bind_param("i", $_SESSION['user_id']);
    $stmt->execute();
    $stmt->bind_result($role);
    $stmt->fetch();
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="it">
<head>
  <meta charset="UTF-8">
  <title>Blog - startSAW</title>
  <link rel="stylesheet" href="../css/style.css">
</head>
<body>
  <h1>Ultimi Articoli del Blog</h1>
  
  <form action="../search/search_results.php" method="get" style="margin-top: 20px;">
		<input type="text" name="q" placeholder="Cerca utenti o post..." required style="padding: 10px; width: 60%;">
		<button type="submit" style="padding: 10px;">🔍 Cerca</button>
  </form>


  <?php if ($role === 'admin' || $role === 'editor'): ?>
    <p><a href="new_post.php"><button>Scrivi un nuovo post</button></a></p>
  <?php endif; ?>

  <?php if ($result->num_rows > 0): ?>
    <ul class="blog-list">
      <?php while ($row = $result->fetch_assoc()): ?>
        <li>
          <a href="view_post.php?id=<?= $row['id'] ?>">
            <?= htmlspecialchars($row['title']) ?>
          </a>
          <small>(<?= date('d/m/Y H:i', strtotime($row['created_at'])) ?>)</small>
        </li>
      <?php endwhile; ?>
    </ul>
  <?php else: ?>
    <p>Nessun articolo presente.</p>
  <?php endif; ?>

  <p><a href="../index.php" class="link-button">← Torna alla Home</a></p>
</body>
</html>

<?php $conn->close(); ?>