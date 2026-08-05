<?php

  // Calls $padCall and returns only its echoed output, like call/ob.php; used by get/go/call
  // and by build/page.php for the page's own PHP file. Blanking a bare 1 return value has no
  // effect here, since the return value is discarded either way.

  include PAD . 'call/_call.php';

  if ( $padCallPHP === 1 )
    $padCallPHP = '';

  return $padCallOB;

?>