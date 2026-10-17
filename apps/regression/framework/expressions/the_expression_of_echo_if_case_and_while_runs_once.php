<?php

  // The expression of {echo}, {if}, {case} and {while} runs once: it was evaluated when the
  // parameters were read and again by the tag, so a PHP call in it ran twice.

  $calls = 0;

  function onceCall () { return ++ $GLOBALS ['calls']; }

?>
