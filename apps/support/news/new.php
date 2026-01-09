<?php
  requireAdmin();

  if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'] ?? '';
    $content = $_POST['content'] ?? '';

    if ($title && $content) {
      $new_id = db("INSERT INTO news (user_id, title, content) VALUES ({0}, '{1}', '{2}')",
                  [$user_id, $title, $content]);
      padRedirect("news/view&id=$new_id");
    }
  }
?>