<?php
session_start();
require_once __DIR__ . '/../db/mysql_credentials.php';

// Controllo accesso
if (!isset($_SESSION['user_id'])) {
    echo "Accesso negato.";
    exit;
}

// Connessione DB
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if ($conn->connect_error) die("Connessione fallita: " . $conn->connect_error);

// Verifica ruolo utente
$stmt = $conn->prepare("SELECT role FROM users WHERE id = ?");
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$stmt->bind_result($role);
$stmt->fetch();
$stmt->close();

if ($role !== 'admin') {
    echo "Accesso riservato agli amministratori.";
    exit;
}

// Cambia ruolo utente
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['user_id'], $_POST['new_role'])) {
    $user_id = (int)$_POST['user_id'];
    $new_role = $_POST['new_role'];

    if (in_array($new_role, ['user', 'editor', 'admin'])) {
        $update = $conn->prepare("UPDATE users SET role = ? WHERE id = ?");
        $update->bind_param("si", $new_role, $user_id);
        $update->execute();
        $update->close();

        // Refresh pagina dopo aggiornamento
        header("Location: index.php");
        exit;
    }
}

// Recupera lista utenti
$result = $conn->query("SELECT id, email, first_name, last_name, role FROM users WHERE id != $user_id ORDER BY last_name");
?>

<!DOCTYPE html>
<html lang="it">
<head>
  <meta charset="UTF-8">
  <title>Gestione Utenti - Admin</title>
  <link rel="stylesheet" href="../css/style.css">
</head>
<body>
  <h1>Gestione Utenti</h1>

  <table border="1" cellpadding="5">
    <tr>
      <th>Nome</th>
      <th>Email</th>
      <th>Ruolo</th>
      <th>Azione</th>
    </tr>

    <?php while ($row = $result->fetch_assoc()): ?>
    <tr>
      <td><?= htmlspecialchars($row['first_name'] . ' ' . $row['last_name']) ?></td>
      <td><?= htmlspecialchars($row['email']) ?></td>
      <td><?= htmlspecialchars($row['role']) ?></td>
      <td>
        <form method="post" style="display:inline;">
          <input type="hidden" name="user_id" value="<?= $row['id'] ?>">
          <select name="new_role">
            <option value="user" <?= $row['role'] == 'user' ? 'selected' : '' ?>>user</option>
            <option value="editor" <?= $row['role'] == 'editor' ? 'selected' : '' ?>>editor</option>
            <option value="admin" <?= $row['role'] == 'admin' ? 'selected' : '' ?>>admin</option>
          </select>
          <button type="submit">Aggiorna</button>
        </form>
      </td>
    </tr>
    <?php endwhile; ?>
  </table>

  <br>
  <form action="modera_commenti.php" method="get">
    <button type="submit">🔍 Modera Commenti</button>
  </form>

  <p><a href="../index.php" class="link-button">← Torna alla Home</a></p>
</body>
</html>

<?php $conn->close(); ?>