<?php

  function isLoggedIn() {
    return isset($_SESSION['user_id']) && $_SESSION['user_id'] > 0;
  }

  function isAdmin() {
    return isLoggedIn() && isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
  }

  function requireLogin() {
    if (!isLoggedIn()) {
      padRedirect('auth/login');
    }
  }

  function requireAdmin() {
    requireLogin();
    if (!isAdmin()) {
      padRedirect('index');
    }
  }

  function hashPassword($password) {
    return password_hash($password, PASSWORD_DEFAULT);
  }

  function verifyPassword($password, $hash) {
    return password_verify($password, $hash);
  }

  function getCurrentUser() {
    if (!isLoggedIn()) return null;
    return db("RECORD * FROM users WHERE id={0}", [$_SESSION['user_id']]);
  }

?>