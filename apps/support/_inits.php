<?php

  session_start();

  // Set title based on page
  $pagePath = $_GET['padPage'] ?? 'index';
  $pageMap = [
    'index' => 'Home',
    'forum/index' => 'Forum',
    'forum/board' => 'Board',
    'forum/topic' => 'Topic',
    'forum/new' => 'New Topic',
    'news/index' => 'News',
    'news/view' => 'Article',
    'news/new' => 'New Article',
    'tickets/index' => 'My Tickets',
    'tickets/view' => 'Ticket',
    'tickets/new' => 'New Ticket',
    'auth/login' => 'Login',
    'auth/register' => 'Register',
    'auth/profile' => 'Profile',
    'auth/logout' => 'Logout'
  ];
  $title = $pageMap[$pagePath] ?? 'Support';

  // Make session variables available to templates
  $user_id = $_SESSION['user_id'] ?? null;
  $username = $_SESSION['username'] ?? null;
  $role = $_SESSION['role'] ?? null;

?>
