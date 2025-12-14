<?php

  requireLogin();

  $title = 'My Tickets';

  // Get user's tickets
  if ($role === 'admin') {
    // Admin sees all tickets
    $tickets = db("ARRAY
      t.*, u.username as author_name,
      (SELECT COUNT(*) FROM ticket_comments WHERE ticket_id = t.id) as comment_count
      FROM tickets t
      JOIN users u ON t.user_id = u.id
      ORDER BY
        CASE t.status WHEN 'open' THEN 1 WHEN 'in_progress' THEN 2 ELSE 3 END,
        t.created_at DESC
    ");
    $showAll = true;
  } else {
    // User sees only their tickets
    $tickets = db("ARRAY
      t.*,
      (SELECT COUNT(*) FROM ticket_comments WHERE ticket_id = t.id) as comment_count
      FROM tickets t
      WHERE t.user_id = {0}
      ORDER BY t.created_at DESC
    ", [$user_id]);
    $showAll = false;
  }

?>
