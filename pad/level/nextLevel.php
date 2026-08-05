<?php

  // Carries out a jump to an outer level requested by a tag handler through
  // $padNextPadLevel - how {continue}, {cease} and {break} unwind. $pad is moved to that
  // level, its remaining template text is thrown away so the current occurrence ends
  // immediately, and the request is cleared.

  $pad = $padNextPadLevel;

  $padOut [$pad] = '';

  $padNextPadLevel = 0;

?>