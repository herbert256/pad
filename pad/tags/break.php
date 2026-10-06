<?php

  // {break 'tag'} stops a loop dead. padFindContinueBreak() picks the level to leave - by
  // name, by number, by negative offset, or the nearest enclosing loop - and its data is
  // emptied so no further occurrence follows. $padNextPadLevel makes level/start.php jump
  // straight back to that level, dropping the rest of the current pass; NULL keeps the
  // tag itself from printing.

  // A {while} or {until} does not iterate a data set - it re-runs its handler while
  // $padWalk is 'next' - so emptying the data left it looping all the same, and {break}
  // behaved as {continue}: {while $i lt 6}...{break}...{/while} ran to 6. The walk is
  // cleared too, so level/end.php asks for no further pass.

  $padNextPadLevel = padFindContinueBreak ( $padParm );

  $padData [$padNextPadLevel] = [];

  // Only a {while}/{until}, which loops while its walk is 'next', needs the walk cleared.
  // A two-pass tag - {markdown}, {tidy}, {form}, toData and the rest - carries 'end' for the
  // pass that post-processes its result, and clearing that dropped the pass: {cease} inside
  // a {markdown} left the Markdown unrendered, a toData loop stored nothing.

  if ( $padWalk [$padNextPadLevel] == 'next' )
    $padWalk [$padNextPadLevel] = '';

  return NULL;

?>
