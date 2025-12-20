<?php

  requireAdmin();

  $title = 'Post News';
  $error = '';
  $formTitle = '';
  $formContent = '';

  if ($edit && db("CHECK news WHERE id = {0}", [$edit])) {
    $title = 'Edit News';
    $formTitle = db("FIELD title FROM news WHERE id = {0}", [$edit]);
    $formContent = db("FIELD content FROM news WHERE id = {0}", [$edit]);
  } else {
    $edit = 0;
  }

  if ($padPost && $action == 'save') {
    $formTitle = trim($news_title ?? '');
    $formContent = trim($content ?? '');

    if (!$formTitle) {
      $error = 'Title is required';
    } elseif (!$formContent) {
      $error = 'Content is required';
    } else {
      if ($edit) {
        db("UPDATE news SET title='{0}', content='{1}', updated_at=NOW() WHERE id={2}",
           [$formTitle, $formContent, $edit]);
        padRedirect("news/view&id=$edit");
      } else {
        $newsId = db("INSERT INTO news (user_id, title, content) VALUES ({0}, '{1}', '{2}')",
                     [$user_id, $formTitle, $formContent]);
        padRedirect("news/view&id=$newsId");
      }
    }
  }

?>
