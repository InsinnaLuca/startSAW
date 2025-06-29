<?php
session_start();
if (isset($_SESSION['user_id'])) {
    require_once __DIR__ . '/../db/mysql_credentials.php';
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    if (!$conn->connect_error) {
        $stmt = $conn->prepare("UPDATE users SET remember_token = NULL WHERE id = ?");
        $stmt->bind_param("i", $_SESSION['user_id']);
        $stmt->execute();
        $stmt->close();
        $conn->close();
    }
}

session_unset();
session_destroy();
setcookie("remember_token", "", time() - 3600, "/"); // cancella cookie
header("Location: ../index.php");
exit;
?>

<!DOCTYPE html>
<html lang="it">
<head>
  <meta charset="UTF-8">
  <title>Logout</title>
  <link rel="stylesheet" href="../css/style.css">
</head>
<body>
  <p>Logout effettuato.</p>
  <p>
    <a href="../index.php">
      <button type="button">Torna alla Home</button>
    </a>
  </p>
</body>
</html>