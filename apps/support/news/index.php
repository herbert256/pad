<?php

  $title = 'News';

  // Get all news with author
  $articles = db("ARRAY
    n.*, u.username as author_name
    FROM news n
    JOIN users u ON n.user_id = u.id
    ORDER BY n.created_at DESC
  ");

?>
