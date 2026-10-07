<?php

  // This site at an address that reaches it without naming it - [::ffff:7f00:1] - which only an
  // address comparison would have to know.

  $url   = parse_url ( $padHost );
  $other = $url ['scheme'] . '://[::ffff:7f00:1]' . ( isset ( $url ['port'] ) ? ':' . $url ['port'] : '' ) . $url ['path']
         . 'regression/pages/?request/debugged_inner&padInclude';

?>
