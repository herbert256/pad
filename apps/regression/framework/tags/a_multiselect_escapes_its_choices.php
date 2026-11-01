<?php

  // Choices holding the characters of HTML and of PAD - from a store, so nothing in the
  // template parts them - are written escaped and stay text, and a posted one is chosen.

  $odd = [ '<b>&amp;</b>', '{php:getcwd}', 'O\'Brien "q"', 'a | b' ];

  $_SERVER ['REQUEST_METHOD'] = 'POST';
  $_POST = [ 'padForm' => 'f', 'odd' => [ '<b>&amp;</b>', '{php:getcwd}', 'O\'Brien "q"' ] ];

?>
