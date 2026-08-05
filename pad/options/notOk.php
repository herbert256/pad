<?php

  // Implements notOk="name": hands 'notOk' to options/go/reset.php, which puts the named
  // content in place of the level's own and turns the failure back into a hit.
  //
  // Included by level/flags.php when the tag did not hit, and by try/catch/level/go.php when
  // the level threw, which is how a tag can show error content instead of raising a PAD error.

  $padReset = 'notOk';

  return include PAD . 'options/go/reset.php';

?>