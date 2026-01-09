<?php
  $id = $_GET['id'] ?? 0;

  if (!$id) {
    padRedirect('forum/index');
  }

  $topic = db("RECORD * FROM forum_topics WHERE id={0}", [$id]);

  if (!$topic) {
    padRedirect('forum/index');
  }

  // Increment view count
  db("UPDATE forum_topics SET views = views + 1 WHERE id={0}", [$id]);

  $topicTitle = $topic['title'];
  $topicId = $topic['id'];
  $boardId = $topic['board_id'];
?>