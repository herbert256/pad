<?php

  // A default reach of 100 candidates, so the terms an expression is looked in are made by the
  // hundred: as far as a value among them, as far as the position a value past them is reached
  // at, and no further for one the terms do not head for. A ceiling of 2000 candidates, so
  // 810000 and 810001 stand past it, where that estimate decides - a value within the ceiling
  // is looked for as far as itself, and 810001 would take the 810,001 terms of '@ ** 2'.

  $padSeqDefaultTries = 100;
  $padSeqMaxTries     = 2000;

?>
