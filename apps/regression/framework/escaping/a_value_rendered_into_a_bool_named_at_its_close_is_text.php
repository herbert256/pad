<?php

  // A {bool} named on its closing tag reduces its content after the walk, its fields' values
  // in it. Content that is values alone is their text's own truth, as {bool 'name', $v}
  // takes it: $a ran as the expression it reads like and stored FALSE, $f called the PHP
  // function it names; 'Hello world' stays TRUE, '0' FALSE. An expression of the author's
  // own still evaluates with a number in it, or a value inside its quotes.

  $a = 'php:strlen(12345) eq 6';
  $f = 'connection_status';
  $t = 'Hello world';
  $z = '0';
  $n = 3;
  $m = 1;
  $u = 'Ann';

?>
