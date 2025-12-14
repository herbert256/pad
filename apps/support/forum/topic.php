<?php

  $topicId = $_GET['id'] ?? 0;

  $topic = db("RECORD
    t.*, b.name as board_name, b.slug as board_slug, u.username as author_name
    FROM forum_topics t
    JOIN forum_boards b ON t.board_id = b.id
    JOIN users u ON t.user_id = u.id
    WHERE t.id = {0}
  ", [$topicId]);

  if (!$topic) {
    padRedirect('forum/index');
  }

  $title = $topic['title'];
  $topicTitle = $topic['title'];
  $boardName = $topic['board_name'];
  $boardSlug = $topic['board_slug'];
  $authorName = $topic['author_name'];
  $createdAt = $topic['created_at'];
  $isLocked = $topic['is_locked'];

  // Increment view count
  db("UPDATE forum_topics SET views = views + 1 WHERE id = {0}", [$topicId]);

  // Get posts
  $posts = db("ARRAY
    p.*, u.username as author_name, u.role as author_role
    FROM forum_posts p
    JOIN users u ON p.user_id = u.id
    WHERE p.topic_id = {0}
    ORDER BY p.created_at ASC
  ", [$topicId]);

  $error = '';
  $success = '';

  // Handle new reply
  if ($_SERVER['REQUEST_METHOD'] == 'POST' && $action == 'reply') {
    if (!$user_id) {
      $error = 'You must be logged in to reply';
    } elseif ($isLocked) {
      $error = 'This topic is locked';
    } else {
      $replyContent = trim($content ?? '');
      if (!$replyContent) {
        $error = 'Reply content is required';
      } else {
        db("INSERT INTO forum_posts (topic_id, user_id, content) VALUES ({0}, {1}, '{2}')",
           [$topicId, $user_id, $replyContent]);
        db("UPDATE forum_topics SET updated_at = NOW() WHERE id = {0}", [$topicId]);
        $success = 'Reply posted successfully';

        // Refresh posts
        $posts = db("ARRAY
          p.*, u.username as author_name, u.role as author_role
          FROM forum_posts p
          JOIN users u ON p.user_id = u.id
          WHERE p.topic_id = {0}
          ORDER BY p.created_at ASC
        ", [$topicId]);
      }
    }
  }

?>
