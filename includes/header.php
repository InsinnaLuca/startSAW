<?php if (session_status() === PHP_SESSION_NONE) session_start(); ?>
<?php require_once __DIR__ . '/../db/mysql_credentials.php'; ?>
<header>
  <nav>
	<link rel="stylesheet" href="/css/style.css">
	<a href="index.php">Home</a>
    <a href="blog/index.php">Blog</a>

    <?php if (isset($_SESSION['user_id'])): ?>
      <a href="profile/show_profile.php">Profilo (<?= htmlspecialchars($_SESSION['first_name']) ?> <?= htmlspecialchars($_SESSION['last_name']) ?>)</a>

      <?php
      // Controlla se l'utente ha ruolo admin
      $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
      if (!$conn->connect_error) {
          $stmt = $conn->prepare("SELECT role FROM users WHERE id = ?");
          $stmt->bind_param("i", $_SESSION['user_id']);
          $stmt->execute();
          $stmt->bind_result($role);
          if ($stmt->fetch() && $role === 'admin') {
              echo '<a href="admin/index.php">Admin</a>';
          }
          $stmt->close();
          $conn->close();
      }
      ?>

      <form action="login/logout.php" method="post" style="display:inline;">
        <button type="submit" class="link-button">Logout</button>
      </form>
    <?php else: ?>
      <a href="login/login.php">Login</a>
      <a href="register/registration.php">Registrati</a>
    <?php endif; ?>
  </nav>
</header>
