<?php

  // Only an action written empty is the page's own address; one of spaces is a relative
  // address, which a <base href> on another site sends there - action=" " and formaction=" "
  // were trimmed first and taken for the page itself, and handed the token to the base.

  $padCsrf = TRUE;

?>
