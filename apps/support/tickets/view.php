<?php

  requireLogin();

  if (!db("CHECK tickets WHERE id = {0}", [$id]))
    padRedirect('tickets/index');

  // Check access: user can only see their own tickets (unless admin)
  $ticketUserId = db("FIELD user_id FROM tickets WHERE id = {0}", [$id]);
  if ($role !== 'admin' && $ticketUserId != $user_id) {
    padRedirect('tickets/index');
  }

  $title = "Ticket #$id";
  $error = '';
  $success = '';

  // Handle actions
  if ($padPost) {

    if ($action == 'comment') {
      $commentContent = trim($content ?? '');
      if (!$commentContent) {
        $error = 'Comment is required';
      } else {
        db("INSERT INTO ticket_comments (ticket_id, user_id, content) VALUES ({0}, {1}, '{2}')",
           [$id, $user_id, $commentContent]);
        db("UPDATE tickets SET updated_at = NOW() WHERE id = {0}", [$id]);
        $success = 'Comment added';
      }
    }

    if ($action == 'status' && $role === 'admin') {
      if (in_array($new_status, ['open', 'in_progress', 'resolved', 'closed'])) {
        db("UPDATE tickets SET status = '{0}', updated_at = NOW() WHERE id = {1}",
           [$new_status, $id]);
        $success = 'Status updated';
      }
    }

    if ($action == 'priority' && $role === 'admin') {
      if (in_array($new_priority, ['low', 'medium', 'high'])) {
        db("UPDATE tickets SET priority = '{0}', updated_at = NOW() WHERE id = {1}",
           [$new_priority, $id]);
        $success = 'Priority updated';
      }
    }
  }

?>
