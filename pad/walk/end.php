<?php

  // Final walk pass: hands the level's accumulated output back to the tag's handler so it
  // can post-process the whole thing.
  //
  // Reached from level/end.php when the tag set $padWalk [$pad] to 'end' on its first
  // pass, as {file}, {tidy} and {trace} do. The handler is called with $padContent set to
  // $padResult [$pad], and whatever it leaves there becomes the level's result.
  //
  // What the handler answers is what level/flags.php reads: the answer was dropped, and the
  // flags were read from the $padTagResult of whichever tag had run last inside the level.

  if ( $padInfo )
    include PAD . 'events/walk.php';

  $padWalk [$pad] = 'end';

  $padContent = $padResult [$pad];
  $padParm    = $padOpt [$pad] [1] ?? '';
  $padTagResult = include PAD . "types/" . $padType [$pad] . ".php";

  $padResult [$pad] = $padContent;

  include PAD . "level/flags.php";

?>
