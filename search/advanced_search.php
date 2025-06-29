<?php
session_start();
?>
<!DOCTYPE html>
<html lang="it">
<head>
  <meta charset="UTF-8">
  <title>Ricerca Avanzata</title>
  <link rel="stylesheet" href="../css/style.css">
</head>
<body>
  <div class="container">
    <h1>Ricerca Avanzata</h1>
    <form action="advanced_results.php" method="get">
      <div>
        <label for="author">Autore (nome o cognome):</label><br>
        <input type="text" name="author" id="author">
      </div>

      <div>
        <label for="keyword">Parola chiave nel titolo o contenuto:</label><br>
        <input type="text" name="keyword" id="keyword">
      </div>

      <div>
        <label for="date">Data (aaaa-mm-gg):</label><br>
        <input type="date" name="date" id="date">
      </div>

      <div style="margin-top: 10px;">
        <input type="submit" value="Cerca">
      </div>
    </form>
  </div>
</body>
</html>