<?php

  $title = 'Home';

  // Get latest news
  $latestNews = db("ARRAY * FROM news ORDER BY created_at DESC LIMIT 5");

  // Get forum stats
  $topicCount = db("FIELD COUNT(*) FROM forum_topics");
  $postCount  = db("FIELD COUNT(*) FROM forum_posts");
  $userCount  = db("FIELD COUNT(*) FROM users");

?>
