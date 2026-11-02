<?php

  // Inspect the final markup, after the nested pass's protected characters are restored.
  function calendarViewOutput ( $source ) {
    return padUnprotect ( padUnescape ( padCode ( $source ) ) );
  }

?>
