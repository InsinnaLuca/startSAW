<?php
session_start();
require_once __DIR__ . '/../db/mysql_credentials.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo "Articolo non valido.";
    exit;
}

$post_id = (int)$_GET['id'];

$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if ($conn->connect_error) die("Connessione fallita: " . $conn->connect_error);

// Recupera il post
$stmt = $conn->prepare("SELECT title, content, created_at FROM blog_posts WHERE id = ?");
$stmt->bind_param("i", $post_id);
$stmt->execute();
$stmt->bind_result($title, $content, $created_at);
if (!$stmt->fetch()) {
    echo "Articolo non trovato.";
    exit;
}
$stmt->close();
?>

<!DOCTYPE html>
<html lang="it">
<head>
  <meta charset="UTF-8">
  <title><?= htmlspecialchars($title) ?></title>
  <link rel="stylesheet" href="../css/style.css">
</head>
<body>
  <h1><?= htmlspecialchars($title) ?></h1>
  <p><em>Pubblicato il <?= date('d/m/Y H:i', strtotime($created_at)) ?></em></p>

  <div>
    <?= $content ?> <!-- HTML già formattato da TinyMCE -->
  </div>

  <p><a href="index.php">← Torna al blog</a></p>

  <hr>

  <!-- Commenti approvati -->
	<h2>Commenti</h2>
	<?php
	$stmt = $conn->prepare("
		SELECT users.first_name, users.last_name, blog_comments.comment, blog_comments.created_at
		FROM blog_comments
		JOIN users ON blog_comments.author_id = users.id
		WHERE blog_comments.post_id = ? AND blog_comments.approved = 1
		ORDER BY blog_comments.created_at DESC
	");
	$stmt->bind_param("i", $post_id);
	$stmt->execute();
	$stmt->bind_result($fname, $lname, $comment_text, $date);
	while ($stmt->fetch()):
	?>
	  <div style="margin-bottom: 20px;">
		<p><strong><?= htmlspecialchars($fname . ' ' . $lname) ?></strong> – <?= date('d/m/Y H:i', strtotime($date)) ?></p>
		<p><?= nl2br(htmlspecialchars($comment_text)) ?></p>
	  </div>
	<?php endwhile;
	$stmt->close();
	?>


  <hr>

  <!-- Form commento -->
  <?php if (isset($_SESSION['user_id'])): ?>
    <h3>Lascia un commento</h3>
    <form action="submit_comment.php" method="post">
      <textarea name="comment" rows="4" required placeholder="Scrivi il tuo commento qui..." style="width:100%;"></textarea><br>
      <input type="hidden" name="post_id" value="<?= $post_id ?>">
      <input type="submit" name="submit" value="Invia commento">
    </form>
  <?php else: ?>
    <p><a href="../login/login.php">Accedi</a> per lasciare un commento.</p>
  <?php endif; ?>

</body>
</html>

<?php $conn->close(); ?>