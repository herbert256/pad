<?php

  // Times every page of every application, as the regression crawl walks them: each page
  // is fetched bare with &padStats, which turns the stats info mode on for that one request
  // when it comes from this machine (inits/info.php), and its PAD-Stats header splits the
  // time into boot, the engine's own work and the application's PHP. The fetches run a
  // dozen at a time, so the times are under load and comparable with each other, not
  // absolute.
  //
  // It read the retired DATA/regression/ store, piped through a bm function that went with
  // apps/check, and keyed its rows by page name, so every application's index overwrote
  // the last - it could never show a row.

  set_time_limit ( 300 );

  $title = 'Benchmark';

  $padCurlStats = TRUE;

  $benchInputs = [];

  foreach ( padAppsList () as $benchPage => $one )
    $benchInputs [$benchPage] = $padHost . $one ['app'] . '/?' . $one ['item'] . '&padInclude&padStats';

  $list = [];

  foreach ( padCurlMulti ( $benchInputs ) as $benchPage => $benchCurl ) {

    if ( ! str_starts_with ( $benchCurl ['result'], '2' ) ) continue;
    if ( ! isset ( $benchCurl ['stats'] ) )                 continue;

    // The stats are nanoseconds, curl's total_time seconds; all of them shown as ms.

    $benchStats = $benchCurl ['stats'];

    $list [$benchPage] = [
      'page'   => $benchPage,
      'curl'   => number_format ( ( $benchCurl ['info'] ['total_time'] ?? 0 ) * 1000, 1 ),
      'total'  => number_format ( ( $benchStats ['total'] ?? 0 ) / 1000000, 1 ),
      'boot'   => number_format ( ( $benchStats ['boot']  ?? 0 ) / 1000000, 1 ),
      'engine' => number_format ( ( $benchStats ['usr']   ?? 0 ) / 1000000, 1 ),
      'app'    => number_format ( ( $benchStats ['call']  ?? 0 ) / 1000000, 1 )
    ];

  }

  ksort ( $list );

  $benchCount = count ( $benchInputs );
  $benchShown = count ( $list );

?>
