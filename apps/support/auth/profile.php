<?php
  requireLogin();

  $currentUser = getCurrentUser();
  $profileUsername = $currentUser['username'];
  $profileEmail = $currentUser['email'];
  $profileRole = $currentUser['role'];
  $profileCreated = $currentUser['created_at'];

  // Get user statistics
  $topicCount = db("FIELD COUNT(*) FROM forum_topics WHERE user_id={0}", [$user_id]);
  $postCount = db("FIELD COUNT(*) FROM forum_posts WHERE user_id={0}", [$user_id]);
  $ticketCount = db("FIELD COUNT(*) FROM tickets WHERE user_id={0}", [$user_id]);
?>