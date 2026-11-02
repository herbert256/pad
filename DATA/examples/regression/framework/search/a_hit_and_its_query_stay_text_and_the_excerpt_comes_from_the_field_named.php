<?php

  // A row whose text holds markup and braces, a query that holds braces: the excerpt is
  // escaped piece by piece, only the marks are markup, and the query is a query.

  $memos = [
    [ 'title' => 'Scripts', 'body' => 'Never trust <script>alert(1)</script> from a form; {php:getcwd} stays text.', 'summary' => 'About scripts in forms.' ]
  ];

  $q = '{php:getcwd} form';

?>
