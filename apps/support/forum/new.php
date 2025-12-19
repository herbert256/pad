<?php

  requireLogin();

  if (!db("CHECK forum_boards WHERE slug = '{0}'", [$board]))
    padRedirect('forum/index');

  $title = 'New Topic';
  $boardId = db("FIELD id FROM forum_boards WHERE slug = '{0}'", [$board]);

  $error = '';
  $formTitle = '';
  $formContent = '';

  if ($padPost && $action == 'create') {
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
