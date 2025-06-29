<?php
session_start();
require_once __DIR__ . '/../db/mysql_credentials.php';

// Verifica accesso e ruolo
if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role'], ['admin', 'editor'])) {
    echo "Accesso negato.";
    exit;
}

// Connessione DB
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if ($conn->connect_error) {
    die("Connessione fallita: " . $conn->connect_error);
}

// Verifica invio form
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])) {
    $title = trim($_POST['title']);
    $content = trim($_POST['content']);
    $author_id = $_SESSION['user_id'];

    if ($title === '' || $content === '') {
        echo "Tutti i campi sono obbligatori.";
        exit;
    }

    $stmt = $conn->prepare("INSERT INTO blog_posts (title, content, author_id) VALUES (?, ?, ?)");
    $stmt->bind_param("ssi", $title, $content, $author_id);

    if ($stmt->execute()) {
        echo "Post pubblicato con successo. <a href='index.php'>Vai al blog</a>";
    } else {
        echo "Errore durante la pubblicazione.";
    }

    $stmt->close();
    $conn->close();
    exit;
} else {
    echo "Richiesta non valida.";
}