<?php

  if ( padClaudeCheck ( ) ) {
    $padErrorAction = 'boot';
    return include_once PAD . "error/types/boot.php";
  }

  include_once PAD . "error/types/$padErrorAction.php";

?>