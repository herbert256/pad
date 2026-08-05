<?php

  // {break 'tag'} stops a loop dead. padFindContinueBreak() picks the level to leave - by
  // name, by number, by negative offset, or the nearest enclosing loop - and its data is
  // emptied so no further occurrence follows. $padNextPadLevel makes level/start.php jump
  // straight back to that level, dropping the rest of the current pass; NULL keeps the
  // tag itself from printing.

  $padNextPadLevel = padFindContinueBreak ( $padParm );

  $padData [$padNextPadLevel] = [];

  return NULL;

?>