<?php

  $title = 'Login';
  $error = '';
  $formUsername = '';

  if ($_SERVER['REQUEST_METHOD'] == 'POST' && $action == 'login') {
    $formUsername = trim($username ?? '');
    $password = $password ?? '';

    if (!$formUsername || !$password) {
      $error = 'Please enter username and password';
    } else {
      $user = db("RECORD * FROM users WHERE username='{0}'", [$formUsername]);

      if ($user && verifyPassword($password, $user['password_hash'])) {
        $_SESSION['user_id']  = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role']     = $user['role'];

        padRedirect('index');
      } else {
        $error = 'Invalid username or password';
      }
    }
  }

?>
