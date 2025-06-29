<?php
session_start();
require_once __DIR__ . '/../db/mysql_credentials.php';

if (!isset($_GET['q']) || trim($_GET['q']) === '') {
    echo "Nessuna ricerca specificata.";
    exit;
}

$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if ($conn->connect_error) die("Errore DB: " . $conn->connect_error);

$term = '%' . $conn->real_escape_string(trim($_GET['q'])) . '%';
?>

<!DOCTYPE html>
<html lang="it">
<head>
  <meta charset="UTF-8">
  <title>Risultati della ricerca</title>
  <link rel="stylesheet" href="../css/style.css">
</head>
<body>
  <main class="container">
    <h1>Risultati per "<?= htmlspecialchars($_GET['q']) ?>"</h1>

    <h2>Articoli del Blog</h2>
    <ul>
    <?php
    $sql_blog = "SELECT id, title FROM blog_posts WHERE title LIKE ? OR content LIKE ?";
    $stmt_blog = $conn->prepare($sql_blog);
    $stmt_blog->bind_param("ss", $term, $term);
    $stmt_blog->execute();
    $result_blog = $stmt_blog->get_result();

    if ($result_blog->num_rows > 0) {
        while ($row = $result_blog->fetch_assoc()) {
            echo '<li><a href="../blog/view_post.php?id=' . $row['id'] . '">' . htmlspecialchars($row['title']) . '</a></li>';
        }
    } else {
        echo '<li>Nessun articolo trovato.</li>';
    }
    $stmt_blog->close();
    ?>
    </ul>

    <h2>Utenti</h2>
    <ul>
    <?php
    $sql_users = "SELECT id, first_name, last_name FROM users WHERE first_name LIKE ? OR last_name LIKE ?";
    $stmt_users = $conn->prepare($sql_users);
    $stmt_users->bind_param("ss", $term, $term);
    $stmt_users->execute();
    $result_users = $stmt_users->get_result();

    if ($result_users->num_rows > 0) {
        while ($row = $result_users->fetch_assoc()) {
            echo '<li><a href="../profile/view_profile.php?id=' . $row['id'] . '">' . htmlspecialchars($row['first_name'] . ' ' . $row['last_name']) . '</a></li>';
        }
    } else {
        echo '<li>Nessun utente trovato.</li>';
    }
    $stmt_users->close();
    $conn->close();
    ?>
    </ul>

    <p><a href="../index.php">← Torna alla home</a></p>
  </main>
</body>
</html>