<?php
  $slug = $_GET['slug'] ?? '';

  if (!$slug) {
    padRedirect('forum/index');
  }

  $board = db("RECORD * FROM forum_boards WHERE slug='{0}'", [$slug]);

  if (!$board) {
    padRedirect('forum/index');
  }

  $boardName = $board['name'];
  $boardId = $board['id'];
?>
