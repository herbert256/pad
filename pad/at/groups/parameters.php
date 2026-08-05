<?php

  // The parameters group: look the path $names up in the positional parameters of level
  // $padIdx. Entry 0 holds the raw, unsplit parameter text and is dropped first, so only
  // the individual parameters 1, 2, ... are searched.

  global $padOpt;

  $padOptAt = $padOpt [$padIdx];

  unset ( $padOptAt [0] );

  return padAtSearch ( $padOptAt , $names );

?>