<?php
  if (isLoggedIn()) {
    padRedirect('index');
  }

  $error = '';

  if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    if ($username && $password) {
      $user = db("RECORD * FROM users WHERE username='{0}'", [$username]);

      if ($user && verifyPassword($password, $user['password_hash'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];
        padRedirect('index');
      } else {
        $error = 'Invalid username or password';
      }
    } else {
      $error = 'Please enter both username and password';
    }
  }
?>
