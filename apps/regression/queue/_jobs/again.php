<?php

  // Asks the scheduler again while it runs, for the minute it runs in - what a second
  // process asking in that minute does: the entry is taken for the minute, so the inner
  // run passes it by instead of running it a second time.

  $GLOBALS ['againRuns'] = ( $GLOBALS ['againRuns'] ?? 0 ) + 1;

  if ( $GLOBALS ['againRuns'] == 1 )
    foreach ( padScheduleRun ( new DateTimeImmutable ( '2026-10-12 08:00', padDateZone () ) ) as $againLine )
      if ( $againLine ['name'] == 'again' )
        $GLOBALS ['againInner'] = $againLine ['status'];

?>
