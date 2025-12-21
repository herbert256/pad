<?php
  // Server data for React components
  $serverTime = date('Y-m-d H:i:s');

  // Stats for dashboard
  $userCount = db("FIELD COUNT(*) FROM users");
  $topicCount = db("FIELD COUNT(*) FROM forum_topics");
  $postCount = db("FIELD COUNT(*) FROM forum_posts");
  $newsCount = db("FIELD COUNT(*) FROM news");
  $ticketCount = db("FIELD COUNT(*) FROM tickets");
?>
