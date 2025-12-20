<?php

  requireLogin();

  $title = 'Submit Ticket';
  $error = '';
  $formTitle = '';
  $formDescription = '';
  $formType = 'bug';
  $formPriority = 'medium';

  if ($padPost && $action == 'create') {
    $formTitle = trim($ticket_title ?? '');
    $formDescription = trim($description ?? '');
    $formType = $type ?? 'bug';
    $formPriority = $priority ?? 'medium';

    if (!$formTitle) {
      $error = 'Title is required';
    } elseif (!$formDescription) {
      $error = 'Description is required';
    } elseif (!in_array($formType, ['bug', 'feature', 'question'])) {
      $error = 'Invalid ticket type';
    } elseif (!in_array($formPriority, ['low', 'medium', 'high'])) {
      $error = 'Invalid priority';
    } else {
      $ticketId = db("INSERT INTO tickets (user_id, type, title, description, priority) VALUES ({0}, '{1}', '{2}', '{3}', '{4}')",
                     [$user_id, $formType, $formTitle, $formDescription, $formPriority]);

      padRedirect("tickets/view&id=$ticketId");
    }
  }

?>