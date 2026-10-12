<?php

  // Comments end where a browser ends them - <!--> and <!---> are empty comments, and --!>
  // ends one as --> does - and a <style> inside an <svg> is an element of the drawing,
  // which </svg> closes, not text up to a </style>. Each button below after such a comment
  // or drawing is live, and posts its form to elsewhere.example: the walk read on to the
  // next --> or </style>, never saw it, and the form kept its token. A button inside a real
  // comment is none to a browser - but a formaction= to another site anywhere in a form's
  // text keeps the token out all the same, settled closed (lib/csrf.php).

  $padCsrf = TRUE;

?>
