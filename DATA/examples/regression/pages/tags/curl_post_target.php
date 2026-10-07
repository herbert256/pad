<?php

  // The other end of curl_posts_its_post_option: the method it was asked with and the
  // posted a - asked for itself, GET and nothing.

  $answer = padRequestMethod () . ':' . ( $_POST ['a'] ?? '' );

?>
