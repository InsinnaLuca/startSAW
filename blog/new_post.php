<?php
session_start();

if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || !in_array($_SESSION['role'], ['admin', 'editor'])) {
    echo "Accesso negato.";
    exit;
}
?>

<!DOCTYPE html>
<html lang="it">
<head>
  <meta charset="UTF-8">
  <title>Nuovo Post</title>
  <!-- TinyMCE -->
  <script src="https://cdn.tiny.cloud/1/obhrbann3y8lohae2zon2evf2cas6r7rqzp5nr10oca8g906/tinymce/7/tinymce.min.js" referrerpolicy="origin"></script>
  <script>
    tinymce.init({
      selector: 'textarea',
      plugins: [
        'anchor', 'autolink', 'charmap', 'codesample', 'emoticons', 'image', 'link', 'lists', 'media',
        'searchreplace', 'table', 'visualblocks', 'wordcount', 'checklist', 'mediaembed', 'casechange',
        'formatpainter', 'pageembed', 'a11ychecker', 'tinymcespellchecker', 'permanentpen', 'powerpaste',
        'advtable', 'advcode', 'editimage', 'advtemplate', 'mentions', 'tinycomments', 'tableofcontents',
        'footnotes', 'mergetags', 'autocorrect', 'typography', 'inlinecss', 'markdown','importword',
        'exportword', 'exportpdf'
      ],
      toolbar: 'undo redo | blocks fontfamily fontsize | bold italic underline strikethrough | ' +
               'link image media table mergetags | addcomment showcomments | spellcheckdialog a11ycheck typography | ' +
               'align lineheight | checklist numlist bullist indent outdent | emoticons charmap | removeformat',
      tinycomments_mode: 'embedded',
      tinycomments_author: '<?= htmlspecialchars($_SESSION["first_name"] . " " . $_SESSION["last_name"]) ?>',
      mergetags_list: [
        { value: 'First.Name', title: 'First Name' },
        { value: 'Email', title: 'Email' },
      ],
    });
  </script>
  <link rel="stylesheet" href="../css/style.css">
</head>
<body>
  <h1>Crea un nuovo post</h1>
  <form action="process_post.php" method="post">
    <label for="title">Titolo:</label><br>
    <input type="text" name="title" id="title" required><br><br>

    <label for="content">Contenuto:</label><br>
    <textarea name="content" id="content" rows="15" cols="80"></textarea><br><br>

    <button type="submit" name="submit">Pubblica</button>
  </form>
</body>
</html>
