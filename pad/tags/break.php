<?php

  // {pad:break 'tag'} stops a loop dead. padFindContinueBreak() picks the level to leave - by
  // name, by number, by negative offset, or the nearest enclosing loop - and its data is
  // emptied so no further occurrence follows. $padNextPadLevel makes level/start.php jump
  // straight back to that level, dropping the rest of the current pass; NULL keeps the
  // tag itself from printing.

  // A {pad:while} or {pad:until} does not iterate a data set - it re-runs its handler while
  // $padWalk is 'next' - so emptying the data left it looping all the same, and {pad:break}
  // behaved as {pad:continue}: {pad:while $i lt 6}...{pad:break}...{/pad:while} ran to 6. The walk is
  // cleared too, so level/end.php asks for no further pass.

  $padNextPadLevel = padFindContinueBreak ( $padParm );

  $padData [$padNextPadLevel] = [];

  // In the @start@ prelude the loop stands on the one row the prelude renders on, and its own
  // rows wait for the body in $padStartData (level/start_end/start1.php): they are emptied
  // too, so no row follows - restored untouched, every one of them rendered after the break.

  if ( $padStartBase [$padNextPadLevel] ?? '' )
    $padStartData [$padNextPadLevel] = [];

  // Only a {pad:while}/{pad:until}, which loops while its walk is 'next', needs the walk cleared.
  // A two-pass tag - {pad:markdown}, {pad:tidy}, {pad:form}, toData and the rest - carries 'end' for the
  // pass that post-processes its result, and clearing that dropped the pass: {pad:cease} inside
  // a {pad:markdown} left the Markdown unrendered, a toData loop stored nothing.

  if ( $padWalk [$padNextPadLevel] == 'next' )
    $padWalk [$padNextPadLevel] = '';

  return NULL;

?>
