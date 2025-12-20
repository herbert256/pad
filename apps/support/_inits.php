<?php

  if (session_status() === PHP_SESSION_NONE) {
    session_start();
  }

  $title = 'PAD Support';

  $user_id      = $_SESSION['user_id']  ?? 0;
  $session_user = $_SESSION['username'] ?? '';
  $role         = $_SESSION['role']     ?? '';

?>