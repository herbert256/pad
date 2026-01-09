<?php
  requireLogin();

  $id = $_GET['id'] ?? 0;

  if (!$id) {
    padRedirect('tickets/index');
  }

  $ticket = db("RECORD * FROM tickets WHERE id={0}", [$id]);

  if (!$ticket) {
    padRedirect('tickets/index');
  }

  // Check if user owns ticket or is admin
  if ($ticket['user_id'] != $user_id && $role !== 'admin') {
    padRedirect('tickets/index');
  }

  $ticketId = $ticket['id'];
  $ticketTitle = $ticket['title'];
  $ticketType = $ticket['type'];
  $ticketStatus = $ticket['status'];
  $ticketPriority = $ticket['priority'];

  // Handle comment submission
  if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['comment'])) {
    $comment = $_POST['comment'];
    if ($comment) {
      db("INSERT INTO ticket_comments (ticket_id, user_id, content) VALUES ({0}, {1}, '{2}')",
         [$ticketId, $user_id, $comment]);
      padRedirect("tickets/view&id=$ticketId");
    }
  }

  // Handle status update (admin only)
  if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['status']) && $role === 'admin') {
    $newStatus = $_POST['status'];
    db("UPDATE tickets SET status='{0}' WHERE id={1}", [$newStatus, $ticketId]);
    padRedirect("tickets/view&id=$ticketId");
  }
?>