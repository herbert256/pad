<?php

  if (!db("CHECK forum_topics WHERE id = {0}", [$id]))
    padRedirect('forum/index');

  $title = db("FIELD title FROM forum_topics WHERE id = {0}", [$id]);

  db("UPDATE forum_topics SET views = views + 1 WHERE id = {0}", [$id]);

  if ($padPost && $action == 'reply') {
    if (!$user_id) {
      $error = 'You must be logged in to reply';
    } elseif (db("FIELD is_locked FROM forum_topics WHERE id = {0}", [$id])) {
      $error = 'This topic is locked';
    } else {
      $replyContent = trim($content ?? '');
      if (!$replyContent) {
        $error = 'Reply content is required';
      } else {
        db("INSERT INTO forum_posts (topic_id, user_id, content) VALUES ({0}, {1}, '{2}')",
           [$id, $user_id, $replyContent]);
        db("UPDATE forum_topics SET updated_at = NOW() WHERE id = {0}", [$id]);
        $success = 'Reply posted successfully';
      }
    }
  }

?>