<?php

  requireAdmin();

  $title = 'Post News';
  $error = '';
  $formTitle = '';
  $formContent = '';
  $editId = $_GET['edit'] ?? 0;

  // Load existing article for editing
  if ($editId) {
    $article = db("RECORD * FROM news WHERE id={0}", [$editId]);
    if ($article) {
      $title = 'Edit News';
      $formTitle = $article['title'];
      $formContent = $article['content'];
    } else {
      $editId = 0;
    }
  }

  if ($_SERVER['REQUEST_METHOD'] == 'POST' && $action == 'save') {
    $formTitle = trim($news_title ?? '');
    $formContent = trim($content ?? '');

    if (!$formTitle) {
      $error = 'Title is required';
    } elseif (!$formContent) {
      $error = 'Content is required';
    } else {
      if ($editId) {
        db("UPDATE news SET title='{0}', content='{1}', updated_at=NOW() WHERE id={2}",
           [$formTitle, $formContent, $editId]);
        padRedirect("news/view&id=$editId");
      } else {
        $newsId = db("INSERT INTO news (user_id, title, content) VALUES ({0}, '{1}', '{2}')",
                     [$user_id, $formTitle, $formContent]);
        padRedirect("news/view&id=$newsId");
      }
    }
  }

?>
