<?php

  requireLogin();

  $ticketId = $_GET['id'] ?? 0;

  $ticket = db("RECORD
    t.*, u.username as author_name
    FROM tickets t
    JOIN users u ON t.user_id = u.id
    WHERE t.id = {0}
  ", [$ticketId]);

  if (!$ticket) {
    padRedirect('tickets/index');
  }

  // Check access: user can only see their own tickets (unless admin)
  if ($role !== 'admin' && $ticket['user_id'] != $user_id) {
    padRedirect('tickets/index');
  }

  $title = "Ticket #{$ticketId}";
  $ticketTitle = $ticket['title'];
  $ticketDescription = $ticket['description'];
  $ticketType = $ticket['type'];
  $ticketStatus = $ticket['status'];
  $ticketPriority = $ticket['priority'];
  $authorName = $ticket['author_name'];
  $createdAt = $ticket['created_at'];
  $isOwner = ($ticket['user_id'] == $user_id);

  // Get comments
  $comments = db("ARRAY
    c.*, u.username as author_name, u.role as author_role
    FROM ticket_comments c
    JOIN users u ON c.user_id = u.id
    WHERE c.ticket_id = {0}
    ORDER BY c.created_at ASC
  ", [$ticketId]);
  $commentCount = count($comments);

  $error = '';
  $success = '';

  // Handle actions
  if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    if ($action == 'comment') {
      $commentContent = trim($content ?? '');
      if (!$commentContent) {
        $error = 'Comment is required';
      } else {
        db("INSERT INTO ticket_comments (ticket_id, user_id, content) VALUES ({0}, {1}, '{2}')",
           [$ticketId, $user_id, $commentContent]);
        db("UPDATE tickets SET updated_at = NOW() WHERE id = {0}", [$ticketId]);
        $success = 'Comment added';

        // Refresh comments
        $comments = db("ARRAY
          c.*, u.username as author_name, u.role as author_role
          FROM ticket_comments c
          JOIN users u ON c.user_id = u.id
          WHERE c.ticket_id = {0}
          ORDER BY c.created_at ASC
        ", [$ticketId]);
        $commentCount = count($comments);
      }
    }

    if ($action == 'status' && $role === 'admin') {
      $newStatus = $new_status ?? '';
      if (in_array($newStatus, ['open', 'in_progress', 'resolved', 'closed'])) {
        db("UPDATE tickets SET status = '{0}', updated_at = NOW() WHERE id = {1}",
           [$newStatus, $ticketId]);
        $ticketStatus = $newStatus;
        $success = 'Status updated';
      }
    }

    if ($action == 'priority' && $role === 'admin') {
      $newPriority = $new_priority ?? '';
      if (in_array($newPriority, ['low', 'medium', 'high'])) {
        db("UPDATE tickets SET priority = '{0}', updated_at = NOW() WHERE id = {1}",
           [$newPriority, $ticketId]);
        $ticketPriority = $newPriority;
        $success = 'Priority updated';
      }
    }
  }

?>
