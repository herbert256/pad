<?php

  // Replay real traffic as tests (pad/lib/replay.php): the stores of recorded GET requests,
  // and one store replayed against the code as it stands - every page whose answer changed,
  // with the first line where it parts from the recording.
  //
  //   ?replay                     the stores
  //   ?replay&store=default       replay the store
  //   &accept=<id> / &acceptAll   take the new answer as the recording
  //   &delete=<id> / &clear       drop a case / the whole store
  //
  // Record with $padRecord = TRUE (or a store name) in an application's _config/config.php,
  // or one request with ?page&padRecord=name.

  $replayStore = padRequestHas ( 'store' ) ? padCoverageName ( padRequest ( 'store' ) ) : '';

  if ( $replayStore and padRequestHas ( 'delete' ) )
    padReplayDelete ( $replayStore, (string) padRequest ( 'delete' ) );

  if ( $replayStore and padRequestHas ( 'clear' ) )
    padReplayDelete ( $replayStore );

  $replayStores = [];

  foreach ( padReplayStores () as $replayOne )
    $replayStores [] = [ 'store' => $replayOne, 'cases' => count ( padReplayCases ( $replayOne ) ) ];

  $replayChanged = [];
  $replayCount   = 0;
  $replaySame    = 0;

  if ( $replayStore and ! padRequestHas ( 'clear' ) ) {

    $replayCases = padReplayCases ( $replayStore );

    foreach ( padReplayRun ( $replayStore ) as $replayOne ) {

      $replayCount++;

      $replayAccept = ( padRequestHas ( 'acceptAll' ) or padRequest ( 'accept', '' ) === $replayOne ['id'] );

      if ( $replayOne ['same'] ) {
        $replaySame++;
        continue;
      }

      if ( $replayAccept and $replayOne ['status'] == 200 ) {
        $replayCase = $replayCases [ $replayOne ['id'] ];
        $replayCase ['body'] = $replayOne ['answer'];
        $replayCase ['host'] = $padHost;
        $replayCase ['when'] = date ( 'Y-m-d H:i:s' );
        padReplaySave ( $replayStore, $replayCase );
        $replaySame++;
        continue;
      }

      $replayChanged [] = [
        'id'     => $replayOne ['id'],
        'url'    => $replayOne ['app'] . '/' . $replayOne ['path'] . '?' . $replayOne ['query'],
        'status' => $replayOne ['status'],
        'line'   => $replayOne ['line'],
        'want'   => $replayOne ['want'],
        'got'    => $replayOne ['got']
      ];

    }

  }

  unset ( $replayOne, $replayCases, $replayCase, $replayAccept );

  $title = 'Replay';

?>
