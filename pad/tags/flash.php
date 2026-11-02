<?php

  // {pad:flash} <p class="{$type}">{$message}</p> {/pad:flash}: the flash messages for this
  // request, one occurrence each, with the message and its type; {pad:flash 'error'} only
  // those of that type. No message, and the @else@ half renders. padFlashShow in
  // lib/flash.php; padFlash ( 'Saved.' ) in the page's PHP sets one.

  $padFlashRows = padFlashShow ( (string) $padParm );

  return count ( $padFlashRows ) ? $padFlashRows : FALSE;

?>
