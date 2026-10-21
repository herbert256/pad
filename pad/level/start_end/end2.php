<?php

  // Second half of the @end@ construct: the loop is exhausted, so the coda put aside by
  // start_end/end1.php becomes the level's text and is rendered against a single default
  // occurrence, after which level/end.php closes the level for real.

  // The coda renders on one default row, and count@ would count that row on top of the
  // occurrences already walked - the Total: {count@users} below a table of three users said 4.
  // The level's own rows are kept for it while the section renders (properties/count.php).

  $padSectionRows [$pad] = count ( $padData [$pad] );

  // The rows themselves are put aside in $padStartData, as start_end/start1.php puts them
  // aside for the prelude: what the level walked is still its data for the end phase - the
  // toData option stored the stand-in row, {items toData='c'}H@start@{$n}@end@F{/items}
  // gave c one row without a field.

  $padStartData [$pad] = $padData [$pad];

  $padBase [$pad] = $padEndBase [$pad];
  $padData [$pad] = padDefaultData ();

  $padEndBase [$pad] = '';

  reset ( $padData [$pad] );

  include PAD . 'occurrence/occurrence.php';

?>
