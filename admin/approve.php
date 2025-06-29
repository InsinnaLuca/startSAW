<?php
session_start();
require_once __DIR__ . '/../db/mysql_credentials.php';

// Verifica accesso e ruolo admin
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    echo "Accesso non autorizzato.";
    exit;
}

if (!isset($_POST['id']) || !is_numeric($_POST['id'])) {
    echo "ID non valido.";
    exit;
}

$id = (int)$_POST['id'];

$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if ($conn->connect_error) {
    die("Connessione fallita: " . $conn->connect_error);
}

$stmt = $conn->prepare("UPDATE blog_comments SET approved = 1 WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$stmt->close();
$conn->close();

header("Location: modera_commenti.php");
exit;