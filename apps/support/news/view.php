<?php

  $newsId = $_GET['id'] ?? 0;

  $article = db("RECORD
    n.*, u.username as author_name
    FROM news n
    JOIN users u ON n.user_id = u.id
    WHERE n.id = {0}
  ", [$newsId]);

  if (!$article) {
    padRedirect('news/index');
  }

  $title = $article['title'];
  $articleTitle = $article['title'];
  $articleContent = $article['content'];
  $authorName = $article['author_name'];
  $createdAt = $article['created_at'];
  $updatedAt = $article['updated_at'];

?>
