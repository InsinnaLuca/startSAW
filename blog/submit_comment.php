<?php
session_start();
require_once __DIR__ . '/../db/mysql_credentials.php';

if (!isset($_SESSION['user_id']) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo "Accesso non autorizzato.";
    exit;
}

$user_id = $_SESSION['user_id'];
$post_id = (int)$_POST['post_id'];
$comment = trim($_POST['comment']);

if ($comment === '') {
    echo "Il commento non può essere vuoto.";
    exit;
}

$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if ($conn->connect_error) die("Connessione fallita: " . $conn->connect_error);

// Recupera ruolo utente
$stmt = $conn->prepare("SELECT role FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($role);
$stmt->fetch();
$stmt->close();

// Determina se approvare subito
$approved = ($role === 'admin') ? 1 : 0;

// Inserisci commento
$stmt = $conn->prepare("INSERT INTO blog_comments (post_id, author_id, comment, approved) VALUES (?, ?, ?, ?)");
$stmt->bind_param("iisi", $post_id, $user_id, $comment, $approved);

if ($stmt->execute()) {
    // Redirect alla pagina del post
    header("Location: view_post.php?id=" . $post_id);
    exit;
} else {
    echo "Errore durante l'invio del commento.";
}

$stmt->close();
$conn->close();
?>