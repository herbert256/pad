<?php

  // The page pagetag iterates a variable of its own name. The query key ?pagetag names the
  // page and is no request value: it was promoted as an empty string, the field search
  // found it before the array set here, and {pagetag} resolved as that empty field.

  $pagetag = [ [ 'name' => 'alpha' ], [ 'name' => 'beta' ] ];

?>
