<?php

  // This site at an address that reaches it without naming it - 0.0.0.0 - which only an
  // address comparison would have to know.

  $url   = parse_url ( $padHost );
  $other = $url ['scheme'] . '://0.0.0.0' . ( isset ( $url ['port'] ) ? ':' . $url ['port'] : '' ) . $url ['path']
         . 'regression/pages/?request/debugged_inner&padInclude';

?>
