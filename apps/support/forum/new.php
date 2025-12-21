<?php
  requireLogin();

  $board_id = $_GET['board_id'] ?? 0;
  $topic_id = $_GET['topic_id'] ?? 0;

  if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($topic_id) {
      // New post to existing topic
      $content = $_POST['content'] ?? '';

      if ($content) {
        db("INSERT INTO forum_posts (topic_id, user_id, content) VALUES ({0}, {1}, '{2}')",
           [$topic_id, $user_id, $content]);
        padRedirect("forum/topic&id=$topic_id");
      }
    } else {
      // New topic
      $title = $_POST['title'] ?? '';
      $content = $_POST['content'] ?? '';

      if ($title && $content && $board_id) {
        $new_topic_id = db("INSERT INTO forum_topics (board_id, user_id, title) VALUES ({0}, {1}, '{2}')",
                          [$board_id, $user_id, $title]);
        db("INSERT INTO forum_posts (topic_id, user_id, content) VALUES ({0}, {1}, '{2}')",
           [$new_topic_id, $user_id, $content]);
        padRedirect("forum/topic&id=$new_topic_id");
      }
    }
  }

  $isReply = $topic_id ? true : false;
?>
