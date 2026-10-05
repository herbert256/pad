<?php

  // Opens an @start@ construct: the part of the level's text before the marker is a prelude
  // that renders once, before the data is walked.
  //
  // The text after @start@ is set aside in $padStartBase [$pad] and the real data in
  // $padStartData [$pad]; the prelude is then rendered against a single default occurrence.
  // level/end.php notices the pending $padStartBase and hands over to start_end/start2.php.

  if ( $padInfo )
    include PAD . 'events/start.php';

  // Cut at the level's own marker, not at the first one - that may be a nested level's.

  $padSectionPos = padOpenClosePos ( $padBase [$pad], '@start@' );

  $padStartBase [$pad] = substr ( $padBase [$pad], $padSectionPos + 7 );
  $padBase      [$pad] = substr ( $padBase [$pad], 0, $padSectionPos );

  $padStartData [$pad] = $padData [$pad];
  $padData [$pad]      = padDefaultData ();

  // The prelude renders on that one default row, and count@ would count it: the level's
  // own rows are kept for it while the section renders (properties/count.php).

  $padSectionRows [$pad] = count ( $padStartData [$pad] );

  reset ( $padData [$pad] );

  include PAD . 'occurrence/occurrence.php';

?>