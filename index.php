<?php
session_start();
require_once __DIR__ . '/db/mysql_credentials.php';

// Connessione DB
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if ($conn->connect_error) die("Connessione fallita: " . $conn->connect_error);

// Recupera ultimi 5 post
$sql = "SELECT blog_posts.id, title, content, created_at, users.username 
        FROM blog_posts
        JOIN users ON blog_posts.author_id = users.id
        ORDER BY created_at DESC
        LIMIT 5";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="it">
<head>
  <meta charset="UTF-8">
  <title>startSAW – Skill & Work</title>
  <link rel="stylesheet" href="css/style.css">
</head>
<body>
  <?php include 'includes/header.php'; ?>

  <main class="container">
    <h1>startSAW</h1>
    <p class="slogan">La piattaforma per mettere in mostra le tue competenze e trovare collaborazioni!</p>

    <?php if (isset($_SESSION['username'])): ?>
      <p class="welcome">Bentornato, <strong><?= htmlspecialchars($_SESSION['username']) ?></strong>!</p>
    <?php else: ?>
      <p><a href="login/login.php">Accedi</a> o <a href="register/register.php">registrati</a> per iniziare.</p>
    <?php endif; ?>

    <section class="blog-preview">
      <h2>Ultimi post dal blog</h2>
      <?php if ($result && $result->num_rows > 0): ?>
        <ul class="post-list">
          <?php while ($row = $result->fetch_assoc()): ?>
            <li>
              <h3><a href="blog/post.php?id=<?= $row['id'] ?>"><?= htmlspecialchars($row['title']) ?></a></h3>
              <p class="meta">Di <?= htmlspecialchars($row['username']) ?> – <?= date('d/m/Y', strtotime($row['created_at'])) ?></p>
              <p><?= substr(strip_tags($row['content']), 0, 100) ?>...</p>
            </li>
          <?php endwhile; ?>
        </ul>
      <?php else: ?>
        <p>Nessun post disponibile.</p>
      <?php endif; ?>
    </section>
  </main>

  <?php include 'includes/footer.php'; ?>
</body>
</html>
