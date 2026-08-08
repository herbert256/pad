<?php

  // Handler for the error option, the documented alias of notOk=: the named content stands in
  // for the level's own when the tag did not hit or threw. It used to reset on the notOk
  // parameter - which an error= tag does not carry - and nothing included it either way, so
  // the alias was dead twice over until the audit wired both.

  $padReset = 'error';

  return include PAD . 'options/go/reset.php';

?>