<?php
session_start();
require_once __DIR__ . '/../db/mysql_credentials.php';

$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if ($conn->connect_error) die("Connessione fallita: " . $conn->connect_error);

// LOGIN AUTOMATICO tramite cookie
if (!isset($_SESSION['user_id']) && isset($_COOKIE['remember_token'])) {
    $token = $_COOKIE['remember_token'];
    $stmt = $conn->prepare("SELECT id, email, first_name, last_name, role FROM users WHERE remember_token = ?");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $stmt->bind_result($id, $email, $first_name, $last_name, $role);
    if ($stmt->fetch()) {
        $_SESSION['user_id'] = $id;
        $_SESSION['email'] = $email;
        $_SESSION['first_name'] = $first_name;
        $_SESSION['last_name'] = $last_name;
        $_SESSION['role'] = $role;
    }
    $stmt->close();
}

// LOGIN MANUALE da form
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])) {
    $email = trim($_POST['email']);
    $password = $_POST['pass'];
    $remember = isset($_POST['remember']);

    $stmt = $conn->prepare("SELECT id, password_hash, first_name, last_name, role FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows === 1) {
        $stmt->bind_result($id, $hash, $first_name, $last_name, $role);
        $stmt->fetch();

        if (password_verify($password, $hash)) {
            $_SESSION['user_id'] = $id;
            $_SESSION['email'] = $email;
            $_SESSION['first_name'] = $first_name;
            $_SESSION['last_name'] = $last_name;
            $_SESSION['role'] = $role;

            if ($remember) {
                $token = bin2hex(random_bytes(32));
                $update = $conn->prepare("UPDATE users SET remember_token = ? WHERE id = ?");
                $update->bind_param("si", $token, $id);
                $update->execute();
                $update->close();
                setcookie("remember_token", $token, time() + (86400 * 30), "/");
            }

            echo "Login avvenuto";
        } else {
            echo "Credenziali errate";
        }
    } else {
        echo "Utente non trovato";
    }

    $stmt->close();
    $conn->close();
	header("Location: ../profile/show_profile.php");
    exit;
}
?>

<!-- ✅ HTML Login Form -->
<!DOCTYPE html>
<html lang="it">
<head>
  <meta charset="UTF-8">
  <title>Login</title>
  <link rel="stylesheet" href="../css/style.css">
</head>
<body>
  <h1>Login</h1>
  <form action="login.php" method="post">
    <input type="email" name="email" required placeholder="Email" id="email">
    <input type="password" name="pass" required placeholder="Password" id="pass">
    <label>
      <input type="checkbox" name="remember" value="1"> Ricordami
    </label>
    <input type="submit" name="submit" value="Accedi">
  </form>
  <p>
  <a href="../index.php">
    <button type="button">Torna alla Home</button>
  </a>
</p>

</body>
</html>