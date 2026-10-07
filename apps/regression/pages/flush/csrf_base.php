<?php

  // A <base href> sent early by {flush} still decides where the forms below it post: the
  // part sent early and the rest are two passes, and the second knew no base - action="save"
  // under a base on another site posted there with the session's token.

  $padCsrf = TRUE;

?>
