<?php

  // Optional groups in optional groups 7 000 deep: the diagram's own error, a rule nested more
  // than 200 deep - where laying the rule out and saying it went down a call a level and PHP
  // ended the request with its maximum call stack size. The page is made here and run as PAD,
  // as a diagram reads its text as it stands.

  $page = "{railroad}\na ::= " . str_repeat ( "('x' ", 7000 ) . str_repeat ( ')?', 7000 ) . "\n{/railroad}";

?>
