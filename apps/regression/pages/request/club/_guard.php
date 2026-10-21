<?php

  // The club fixture: a guard that reads the user right after padAuthRequire, as a guard
  // may - for a guest asked directly padAuthRequire sends the visitor away and nothing after
  // it runs. Included by a {page} of a public page, the refusal ends the guard there too.

  padAuthRequire ( 'request/vars' );

  $member = padUser ();

  return $member ['role'] == 'member';

?>
