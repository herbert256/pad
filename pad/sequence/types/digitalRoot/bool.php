<?php

  // Membership test for digitalRoot: its terms are the digits 0 to 9, so that is the whole
  // test. build/check.php asks it for {keep digitalRoot}, {remove digitalRoot} and {flag
  // digitalRoot}.
  //
  // Not the type's build: pqBuild() ranks function.php before bool.php.

  function pqBoolDigitalRoot ( $x, $p=0 ) {

    return pqBoolWhole ( $x ) and $x >= 0 and $x <= 9;

  }

?>
