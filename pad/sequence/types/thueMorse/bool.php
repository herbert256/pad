<?php

  // Membership test for thueMorse: its terms are 0 and 1, so that is the whole test.
  // build/check.php asks it for {keep thueMorse}, {remove thueMorse} and {flag thueMorse}.
  //
  // Not the type's build: pqBuild() ranks function.php before bool.php.

  function pqBoolThueMorse ( $x, $p=0 ) {

    return pqBoolWhole ( $x ) and ( $x == 0 or $x == 1 );

  }

?>
