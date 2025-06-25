<header>
  <nav>
  <a href="index.php">Home</a>

  <?php if (isset($_SESSION['user_id'])): ?>
    <a href="profile/view_profile.php">Profilo</a>
    <form action="login/logout.php" method="post" style="display:inline;">
      <button type="submit" style="background:none;border:none;color:#0066cc;cursor:pointer;">Logout</button>
    </form>
  <?php else: ?>
    <a href="login/login.php">Login</a>
    <a href="register/registration.php">Registrati</a>
  <?php endif; ?>
</nav>

</header>