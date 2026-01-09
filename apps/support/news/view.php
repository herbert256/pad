<?php
  $id = $_GET['id'] ?? 0;

  if (!$id) {
    padRedirect('news/index');
  }

  $article = db("RECORD * FROM news WHERE id={0}", [$id]);

  if (!$article) {
    padRedirect('news/index');
  }

  $articleTitle = $article['title'];
  $articleContent = $article['content'];
  $articleDate = $article['created_at'];
  $articleUserId = $article['user_id'];
?>