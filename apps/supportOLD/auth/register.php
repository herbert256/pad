<?php

  $title = 'Register';
  $error = '';
  $success = '';
  $formUsername = '';
  $formEmail = '';

  if ($_SERVER['REQUEST_METHOD'] == 'POST' && $action == 'register') {
    $formUsername = trim($username ?? '');
    $formEmail = trim($email ?? '');
    $password = $password ?? '';
    $password2 = $password2 ?? '';

    if (!$formUsername || !$formEmail || !$password) {
      $error = 'All fields are required';
    } elseif (strlen($formUsername) < 3) {
      $error = 'Username must be at least 3 characters';
    } elseif (!filter_var($formEmail, FILTER_VALIDATE_EMAIL)) {
      $error = 'Invalid email address';
    } elseif (strlen($password) < 6) {
      $error = 'Password must be at least 6 characters';
    } elseif ($password !== $password2) {
      $error = 'Passwords do not match';
    } else {

      $exists = db("CHECK users WHERE username='{0}'", [$formUsername]);
      if ($exists) {
        $error = 'Username already taken';
      } else {

        $exists = db("CHECK users WHERE email='{0}'", [$formEmail]);
        if ($exists) {
          $error = 'Email already registered';
        } else {

          $hash = hashPassword($password);
          db("INSERT INTO users (username, email, password_hash) VALUES ('{0}', '{1}', '{2}')",
             [$formUsername, $formEmail, $hash]);

          $success = 'Account created successfully! You can now login.';
          $formUsername = '';
          $formEmail = '';
        }
      }
    }
  }

?>