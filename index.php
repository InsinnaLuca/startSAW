<?php
session_start();
require_once __DIR__ . '/db/mysql_credentials.php';

// Connessione DB
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if ($conn->connect_error) die("Connessione fallita: " . $conn->connect_error);

// Recupera ultimi 5 post
$sql = "SELECT blog_posts.id, blog_posts.title, blog_posts.content, blog_posts.created_at, 
               CONCAT(users.first_name, ' ', users.last_name) AS author_name
        FROM blog_posts
        JOIN users ON blog_posts.author_id = users.id
        ORDER BY blog_posts.created_at DESC
        LIMIT 5";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="it">
<head>
  <meta charset="UTF-8">
  <title>startSAW</title>
  <link rel="stylesheet" href="css/style.css">
</head>
<body>
  <?php include 'includes/header.php'; ?>

  <main class="container">
    <div class="logo-container">
		<h1>startSAW</h1>
		<img src="images/logo.png" alt="startSAW Logo" width="50">
	</div>

    <p class="slogan">La piattaforma per mettere in mostra le tue competenze e trovare collaborazioni!</p>

    <?php if (isset($_SESSION['first_name'], $_SESSION['last_name'])): ?>
		<p class="welcome">Bentornato, <strong><?= htmlspecialchars($_SESSION['first_name'] . ' ' . $_SESSION['last_name']) ?></strong>!</p>
	<?php else: ?>

      <p><a href="login/login.php">Accedi</a> o <a href="register/registration.php">registrati</a> per iniziare.</p>
    <?php endif; ?>
	
	<form action="search/search_results.php" method="get" style="margin-top: 20px;">
		<input type="text" name="q" placeholder="Cerca utenti o post..." required style="padding: 10px; width: 60%;">
		<button type="submit" style="padding: 10px;">🔍 Cerca</button>
	</form>
	<p><a href="search/advanced_search.php" class="link-button">🔍 Ricerca Avanzata</a></p>

    <section class="blog-preview">
      <h2>Ultimi 5 post dal blog</h2>
      <?php if ($result && $result->num_rows > 0): ?>
        <ul class="post-list">
          <?php while ($row = $result->fetch_assoc()): ?>
            <li>
              <h3><a href="blog/view_post.php?id=<?= $row['id'] ?>"><?= htmlspecialchars($row['title']) ?></a></h3>
              <p class="meta">Di <?= htmlspecialchars($row['author_name']) ?> – <?= date('d/m/Y', strtotime($row['created_at'])) ?></p>
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
