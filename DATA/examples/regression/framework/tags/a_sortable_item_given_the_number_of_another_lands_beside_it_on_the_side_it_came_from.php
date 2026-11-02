<?php

  // The form came back with the numbers a visitor without scripting typed: e, last, given 2,
  // the number of b - moved up, it lands after b; a, first, given 4, the number of d - moved
  // down, it lands before d. Items of one number keep the order they had, as asort sorts
  // them, and as TAGS.md says. The post is set here, so the case is one fetch.

  $_SERVER ['REQUEST_METHOD'] = 'POST';
  $_POST ['padForm'] = 'r';
  $_POST ['order']   = [ 'a' => '4', 'b' => '2', 'c' => '3', 'd' => '4', 'e' => '2' ];

?>
