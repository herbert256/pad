<?php

  // {continue 'tag'} abandons the rest of the current pass of a loop and lets it carry on
  // with its next element. padFindContinueBreak() picks the level - by name, by number,
  // by negative offset, or the nearest enclosing loop - and $padNextPadLevel makes
  // level/start.php jump back to it, throwing away what was left of the occurrence. The
  // loop's data is untouched, which is the difference with {break}.

  $padNextPadLevel = padFindContinueBreak ( $padParm );

  return TRUE;

?>