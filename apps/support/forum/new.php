<?php

  requireLogin();

  $boardSlug = $_GET['board'] ?? '';

  $board = db("RECORD * FROM forum_boards WHERE slug='{0}'", [$boardSlug]);

  if (!$board) {
    padRedirect('forum/index');
  }

  $title = 'New Topic';
  $boardId = $board['id'];
  $boardName = $board['name'];

  $error = '';
  $formTitle = '';
  $formContent = '';

  if ($_SERVER['REQUEST_METHOD'] == 'POST' && $action == 'create') {
    $formTitle = trim($topic_title ?? '');
    $formContent = trim($content ?? '');

    if (!$formTitle) {
      $error = 'Topic title is required';
    } elseif (!$formContent) {
      $error = 'Topic content is required';
    } else {
      // Create topic
      $topicId = db("INSERT INTO forum_topics (board_id, user_id, title) VALUES ({0}, {1}, '{2}')",
                    [$boardId, $user_id, $formTitle]);

      // Create first post
      db("INSERT INTO forum_posts (topic_id, user_id, content) VALUES ({0}, {1}, '{2}')",
         [$topicId, $user_id, $formContent]);

      padRedirect("forum/topic&id=$topicId");
    }
  }

?>
