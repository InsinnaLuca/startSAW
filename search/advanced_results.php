<?php
require_once __DIR__ . '/../db/mysql_credentials.php';
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if ($conn->connect_error) die("Connessione fallita: " . $conn->connect_error);

$author = '%' . $conn->real_escape_string($_GET['author'] ?? '') . '%';
$keyword = '%' . $conn->real_escape_string($_GET['keyword'] ?? '') . '%';
$date = $_GET['date'] ?? '';

// Query SQL avanzata
$query = "SELECT b.id, b.title, b.created_at, u.first_name, u.last_name 
          FROM blog_posts b 
          JOIN users u ON b.author_id = u.id 
          WHERE (u.first_name LIKE ? OR u.last_name LIKE ?)
            AND (b.title LIKE ? OR b.content LIKE ?)";
if ($date) {
    $query .= " AND DATE(b.created_at) = ?";
}

$stmt = $conn->prepare($query);
if ($date) {
    $stmt->bind_param("sssss", $author, $author, $keyword, $keyword, $date);
} else {
    $stmt->bind_param("ssss", $author, $author, $keyword, $keyword);
}
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="it">
<head>
  <meta charset="UTF-8">
  <title>Risultati Ricerca Avanzata</title>
  <link rel="stylesheet" href="../css/style.css">
</head>
<body>
  <div class="container">
    <h1>Risultati della Ricerca Avanzata</h1>
    <?php if ($result->num_rows > 0): ?>
      <ul>
        <?php while ($row = $result->fetch_assoc()): ?>
          <li>
            <a href="../blog/view_post.php?id=<?= $row['id'] ?>">
              <?= htmlspecialchars($row['title']) ?>
            </a><br>
            <small>Di <?= htmlspecialchars($row['first_name']) ?> <?= htmlspecialchars($row['last_name']) ?> – <?= htmlspecialchars($row['created_at']) ?></small>
          </li>
        <?php endwhile; ?>
      </ul>
    <?php else: ?>
      <p>Nessun risultato trovato.</p>
    <?php endif; ?>
  </div>
</body>
</html>
<?php
$stmt->close();
$conn->close();
?>
