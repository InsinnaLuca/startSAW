<?php
session_start();
require_once __DIR__ . '/../db/mysql_credentials.php';

// Verifica accesso e ruolo admin
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    echo "Accesso riservato agli amministratori.";
    exit;
}

$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if ($conn->connect_error) {
    die("Connessione fallita: " . $conn->connect_error);
}

// Recupera commenti non approvati
$stmt = $conn->prepare("
    SELECT blog_comments.id, users.first_name, users.last_name, blog_comments.comment, blog_posts.title
    FROM blog_comments
    JOIN users ON blog_comments.author_id = users.id
    JOIN blog_posts ON blog_comments.post_id = blog_posts.id
    WHERE blog_comments.approved = 0
");
$stmt->execute();
$stmt->bind_result($id, $fname, $lname, $comment_text, $post_title);

// Output commenti in attesa
echo "<h1>Commenti da approvare</h1>";
while ($stmt->fetch()) {
    echo "<div style='margin-bottom:20px;'>
            <p><strong>" . htmlspecialchars($fname) . " " . htmlspecialchars($lname) . "</strong> su <em>" . htmlspecialchars($post_title) . "</em></p>
            <p>" . nl2br(htmlspecialchars($comment_text)) . "</p>
            <form action='approve.php' method='post'>
                <input type='hidden' name='id' value='" . htmlspecialchars($id) . "'>
                <button type='submit'>Approva</button>
            </form>
          </div><hr>";
}
$stmt->close();
$conn->close();
?>