<?php
  requireLogin();

  if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $type = $_POST['type'] ?? 'question';
    $title = $_POST['title'] ?? '';
    $description = $_POST['description'] ?? '';
    $priority = $_POST['priority'] ?? 'medium';

    if ($title && $description) {
      $new_id = db("INSERT INTO tickets (user_id, type, title, description, priority) VALUES ({0}, '{1}', '{2}', '{3}', '{4}')",
                  [$user_id, $type, $title, $description, $priority]);
      padRedirect("tickets/view&id=$new_id");
    }
  }
?>