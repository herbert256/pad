<?php

  $u = [ 'first-name' => 'Annie', 'e-mail' => 'a@b.c', 'x#y' => 'hash', 'p%q' => 'pct' ];

  // A key part still refuses what makes it a path or a glob.

  $refused = '';
  foreach ( [ 'a/../../etc/x@y', 'x*?[@q', 'a\\b@q', "a\0b@q" ] as $one )
    $refused .= padValid ( $one ) ? 'y' : 'n';

?>
