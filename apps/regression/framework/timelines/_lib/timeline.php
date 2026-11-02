<?php
  function timelineViewOutput ( $source ) {
    return padUnprotect ( padUnescape ( padCode ( $source ) ) );
  }
?>
