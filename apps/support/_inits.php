<?php

  // Start session if not started
  if (session_status() === PHP_SESSION_NONE) {
    session_start();
  }

  // Set page title
  $title = 'PAD Support';

  // Get session variables for templates
  $user_id      = $_SESSION['user_id']  ?? 0;
  $session_user = $_SESSION['username'] ?? '';
  $role         = $_SESSION['role']     ?? '';

?>
