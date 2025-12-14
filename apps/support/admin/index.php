<?php

  $title = 'Admin Dashboard';

  // Get stats
  $userCount = db("FIELD COUNT(*) FROM users");
  $topicCount = db("FIELD COUNT(*) FROM forum_topics");
  $postCount = db("FIELD COUNT(*) FROM forum_posts");
  $newsCount = db("FIELD COUNT(*) FROM news");
  $ticketCount = db("FIELD COUNT(*) FROM tickets");
  $openTickets = db("FIELD COUNT(*) FROM tickets WHERE status IN ('open', 'in_progress')");

  // Recent users
  $recentUsers = db("ARRAY * FROM users ORDER BY created_at DESC LIMIT 10");

  // Open tickets
  $pendingTickets = db("ARRAY
    t.*, u.username as author_name
    FROM tickets t
    JOIN users u ON t.user_id = u.id
    WHERE t.status IN ('open', 'in_progress')
    ORDER BY
      CASE t.priority WHEN 'high' THEN 1 WHEN 'medium' THEN 2 ELSE 3 END,
      t.created_at ASC
    LIMIT 10
  ");

?>
