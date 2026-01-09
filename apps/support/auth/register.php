<?php
  if (isLoggedIn()) {
    padRedirect('index');
  }

  $error = '';

  if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $confirm = $_POST['confirm'] ?? '';

    if (!$username || !$email || !$password || !$confirm) {
      $error = 'All fields are required';
    } elseif ($password !== $confirm) {
      $error = 'Passwords do not match';
    } elseif (db("CHECK users WHERE username='{0}'", [$username])) {
      $error = 'Username already exists';
    } elseif (db("CHECK users WHERE email='{0}'", [$email])) {
      $error = 'Email already exists';
    } else {
      $passwordHash = hashPassword($password);
      $new_id = db("INSERT INTO users (username, email, password_hash, role) VALUES ('{0}', '{1}', '{2}', 'user')",
                  [$username, $email, $passwordHash]);

      $_SESSION['user_id'] = $new_id;
      $_SESSION['username'] = $username;
      $_SESSION['role'] = 'user';
      padRedirect('index');
    }
  }
?>