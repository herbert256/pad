<?php
  function timeViewOutput ( $source ) {
    return padUnprotect ( padUnescape ( padCode ( $source ) ) );
  }
?>
