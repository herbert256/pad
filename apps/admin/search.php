<?php

  // Text searched for in the sources of one application, or of all: every line that holds
  // it, with the file and line number, linking to the file. Literal text, case ignored
  // unless asked; at most $searchMax hits.

  $title      = 'Search';
  $app        = adminAppAsked ();
  $searchFor  = trim ( adminGet ( 'q' ) );
  $searchCase = adminGet ( 'case' ) === '1' ? 1 : 0;
  $searchMax  = 500;
  $hits       = [];
  $searched   = 0;
  $truncated  = 0;

  $appRows = [];

  foreach ( adminApps () as $name => $one )
    $appRows [] = [ 'name' => $name, 'selected' => $name === $app ? 1 : 0 ];

  if ( strlen ( $searchFor ) >= 2 ) {

    $title = "Search: $searchFor";

    foreach ( $app === '' ? adminApps () : [ $app => adminApps () [$app] ] as $name => $one ) {

      foreach ( adminFilesIn ( $one ['dir'] ) as $file ) {

        if ( ! preg_match ( '/\.(pad|php|html|js|mjs|css|json|md|sql|ya?ml|txt|xml|csv)$/i', $file ) or filesize ( $file ) > 1048576 )
          continue;

        $searched++;

        $text = (string) file_get_contents ( $file );

        if ( ( $searchCase ? strpos ( $text, $searchFor ) : stripos ( $text, $searchFor ) ) === FALSE )
          continue;

        foreach ( explode ( "\n", $text ) as $number => $line ) {

          if ( ( $searchCase ? strpos ( $line, $searchFor ) : stripos ( $line, $searchFor ) ) === FALSE )
            continue;

          if ( count ( $hits ) >= $searchMax ) {
            $truncated = 1;
            break 3;
          }

          $hits [] = [ 'app' => $name, 'file' => substr ( $file, strlen ( $one ['dir'] ) ), 'line' => $number + 1,
                       'text' => mb_strimwidth ( trim ( adminRedact ( $line ) ), 0, 220, '...' ) ];

        }

      }

    }

  }

  $hitCount = count ( $hits );
  $asked    = strlen ( $searchFor ) >= 2 ? 1 : 0;

?>
