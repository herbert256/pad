<?php

  // Nested rows - the children of a row in its field sub= - read by key= and field=: texts
  // and values holding the characters of HTML and of PAD are written escaped and stay text,
  // in the radios, their labels and the path of the choice, value=, on the button.

  $odd = [
    [ 'code' => 'a&b', 'title' => '<b>Bold</b>', 'sub' => [
      [ 'code' => '{php:getcwd}', 'title' => 'O\'Brien "q"' ],
      [ 'code' => 'x|y', 'title' => 'a | b' ] ] ]
  ];

  $pick = '{php:getcwd}';

?>
