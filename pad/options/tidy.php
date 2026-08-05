<?php

  // Implements the tidy option: runs the finished text through padTidy() to normalise its
  // whitespace. End-phase option, so it works on $padResult [$pad].

  $padContent = padTidy ( $padContent, TRUE );

?>