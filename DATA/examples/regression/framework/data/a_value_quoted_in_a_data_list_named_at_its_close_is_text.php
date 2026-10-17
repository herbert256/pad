<?php

  // In a {data} list named on its closing tag a value written in quotes is an element's
  // text, and a number is the number - the content is walked before it is stored, and a
  // value standing bare there is refused (errors/syntax).

  $x = 'php:strlen(12345)';
  $n = 7;

?>
