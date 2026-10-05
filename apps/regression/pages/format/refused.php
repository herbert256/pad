<?php

  // Asked outright for a format the page cannot give, the answer is 406: a page that exposes
  // nothing, a format that does not exist, CSV from a page with no list.

  $a = padCurl ( $padGoExt . 'format/host&padFormat=json' );
  $b = padCurl ( $padGoExt . 'format/orders&padFormat=xml' );
  $c = padCurl ( $padGoExt . 'format/scalar&padFormat=csv' );
  $d = padCurl ( $padGoExt . 'format/scalar&padFormat=json' );

  echo $a ['result'], ' ', $b ['result'], ' ', $c ['result'], ' ', $d ['result'], ' ', json_encode ( json_decode ( $d ['data'] ) );

?>
