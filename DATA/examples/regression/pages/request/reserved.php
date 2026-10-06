<?php

  // A request parameter named this is left alone: PHP refuses to assign $this, and promoting
  // it ended the request with a 500. The fetch carries one beside an ordinary a, which still
  // arrives.

  $curl = padCurl ( $padGoExt . 'request/vars&padInclude&this=x&a=1' );

  $reservedResult = $curl ['result'] . ' ' . trim ( $curl ['data'] );

?>
