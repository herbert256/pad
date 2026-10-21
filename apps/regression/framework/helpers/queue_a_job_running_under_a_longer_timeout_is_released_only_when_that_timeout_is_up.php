<?php

  // A job claimed by a worker with a timeout of 300 seconds (pad work --timeout=300), 95
  // seconds into its run: a worker with the default 60 lets it be - its own worker's time is
  // not up - and releases it only once that time and the margin are.

  $dir = padQueueDir ( 'longrun' );

  foreach ( glob ( $dir . '*' ) ?: [] as $file )
    @unlink ( $file );

  padQueue ( '@mail', [ 'to' => 'ann@example.com', 'template' => '', 'subject' => 'Long' ], queue: 'longrun' );

  [ $run, $record ] = padQueueClaim ( $dir, 300 );

  touch ( $run, time () - 95 );
  $early = padQueueRelease ( $dir, 60 );

  touch ( $run, time () - 335 );
  $late = padQueueRelease ( $dir, 60 );

  foreach ( glob ( $dir . '*' ) ?: [] as $file )
    @unlink ( $file );

  $r = json_encode ( [ 'kept' => $record ['timeout'] ?? 'none', 'early' => $early, 'late' => $late ] );

?>
