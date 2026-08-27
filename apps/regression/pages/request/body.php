<?php

  // The raw-body fixture: what php://input holds, with its length - a request body that
  // is not a form still reaches the page.

  $raw = file_get_contents ( 'php://input' );

  echo 'len:' . strlen ( $raw ) . ' body:' . $raw;

?>