<?php

  requireLogin();

  $title = 'My Profile';
  $error = '';
  $success = '';

  $user = getCurrentUser();
  $formEmail = $user['email'];

  if ($_SERVER['REQUEST_METHOD'] == 'POST' && $action == 'update') {
    $formEmail = trim($email ?? '');
    $currentPassword = $current_password ?? '';
    $newPassword = $new_password ?? '';
    $newPassword2 = $new_password2 ?? '';

    if (!filter_var($formEmail, FILTER_VALIDATE_EMAIL)) {
      $error = 'Invalid email address';
    } elseif ($formEmail !== $user['email']) {
      // Check if email is taken
      $exists = db("CHECK users WHERE email='{0}' AND id<>{1}", [$formEmail, $user_id]);
      if ($exists) {
        $error = 'Email already in use';
      }
    }

    // Password change
    if (!$error && $newPassword) {
      if (!$currentPassword) {
        $error = 'Current password required to change password';
      } elseif (!verifyPassword($currentPassword, $user['password_hash'])) {
        $error = 'Current password is incorrect';
      } elseif (strlen($newPassword) < 6) {
        $error = 'New password must be at least 6 characters';
      } elseif ($newPassword !== $newPassword2) {
        $error = 'New passwords do not match';
      }
    }

    if (!$error) {
      if ($newPassword) {
        $hash = hashPassword($newPassword);
        db("UPDATE users SET email='{0}', password_hash='{1}' WHERE id={2}",
           [$formEmail, $hash, $user_id]);
      } else {
        db("UPDATE users SET email='{0}' WHERE id={1}", [$formEmail, $user_id]);
      }
      $success = 'Profile updated successfully';
      $user = getCurrentUser();
    }
  }

  $memberSince = $user['created_at'];

?>
