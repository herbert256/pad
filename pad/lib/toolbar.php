<?php

  // The debug toolbar: a collapsible bar at the foot of a local text/html page, showing what
  // the engine collected while it rendered - the time and memory it took, the tag tree, the
  // SQL statements, the page and fragment cache, the template files read, the application's
  // variables and the session.
  //
  // Opt-in with $padToolbar = 'local' (TRUE means the same). It never shows to a request from
  // elsewhere: what it lists - SQL, variables, the session - is the same material the error
  // report keeps for this machine, so padLocal() decides, and $padDiagnostics = FALSE closes
  // it like the rest. It stays out of a bare fragment (&padInclude), out of anything that is
  // not a web page answering 200, and out of the page cache: it is added to the response on
  // its way out, after the cache stored the page.
  //
  // padToolbarOn     whether this request gets the bar
  // padToolbarLevel  called by level/setup.php for every level opened: one row of the tree
  // padToolbarAdd    exits/output.php hands the finished page over; the bar goes in before
  //                  its last </body>, or at the end
  //
  // The state lives in $padToolbarData, which a nested pass ({page}, {code}) does not put
  // back to what it was before - lib/checks.php keeps it out of that snapshot - so the
  // levels and the fragment hits of a page built inside the page are counted too.

  function padToolbarOn () {

    global $padToolbar, $padInclude, $padOutputType;

    if ( ! $padToolbar or $padInclude or $padOutputType != 'web' )
      return FALSE;

    return padLocal ();

  }

  function padToolbarLevel () {

    global $pad, $padTag, $padType, $padOrg, $padToolbarData;

    if ( ! padToolbarOn () )
      return;

    $count = count ( $padToolbarData ['tags'] ?? [] );

    if ( $count >= 500 ) {
      $padToolbarData ['more'] = ( $padToolbarData ['more'] ?? 0 ) + 1;
      return;
    }

    // The root level of a page run is the engine's own, not a tag of the template.

    if ( ( $padTag [$pad] ?? '' ) == 'internal' )
      return;

    $padToolbarData ['tags'] [] = [ $pad, $padTag [$pad] ?? '', $padType [$pad] ?? '', $padOrg [$pad] ?? '' ];

  }

  function padToolbarAdd () {

    global $padOutput, $padStop, $padContentType, $padCacheStop, $padCacheServerGzip;

    if ( $padStop != 200 or ! padToolbarOn () or ! str_starts_with ( (string) $padContentType, 'text/html' ) )
      return;

    // A page from the page cache may still be gzipped; the bar goes into the page itself,
    // and the writer then sees a plain body.

    if ( $padCacheStop == 200 and $padCacheServerGzip ) {
      $padOutput          = padUnzip ( $padOutput );
      $padCacheServerGzip = FALSE;
    }

    $bar = padToolbarHtml ();
    $at  = strripos ( $padOutput, '</body>' );

    $padOutput = ( $at === FALSE ) ? $padOutput . $bar : substr_replace ( $padOutput, $bar, $at, 0 );

  }

  function padToolbarHtml () {

    global $padHR, $padLvlId, $padCache, $padCacheStop, $padApp, $padPage, $padOutput;
    global $padToolbarData, $padSrcRead, $_SQL;

    $time   = round ( ( hrtime ( TRUE ) - $padHR ) / 1e6, 1 );
    $memory = round ( memory_get_peak_usage () / 1048576, 1 );
    $tags   = $padToolbarData ['tags'] ?? [];
    $sql    = (array) ( $_SQL ?? [] );
    $files  = array_map ( 'padSrcName', array_keys ( $padSrcRead ?? [] ) );
    $hits   = $padToolbarData ['fragmentHit']  ?? 0;
    $misses = $padToolbarData ['fragmentMiss'] ?? 0;

    if     ( ! $padCache           ) $cache = 'page cache off';
    elseif ( $padCacheStop == 200  ) $cache = 'page cache hit';
    else                             $cache = 'page cache miss';

    $summary = "PAD $padApp/$padPage · {$time} ms · {$memory} MB · $padLvlId levels · " . count ( $sql ) . " SQL · $cache";

    if ( $hits or $misses )
      $summary .= " · fragments $hits hit, $misses miss";

    $tree = '';

    foreach ( $tags as $row )
      $tree .= str_repeat ( '  ', max ( 0, $row [0] ) ) . '{' . padUnprotect ( $row [3] !== '' ? $row [3] : $row [1] ) . '}'
             . ( $row [2] !== '' ? "  · {$row[2]}" : '' ) . "\n";

    if ( $padToolbarData ['more'] ?? 0 )
      $tree .= '... ' . $padToolbarData ['more'] . " more\n";

    $request = [
      'application' => $padApp,
      'page'        => $padPage,
      'time'        => "$time ms",
      'memory'      => "$memory MB peak",
      'levels'      => $padLvlId,
      'output'      => strlen ( (string) $padOutput ) . ' bytes',
      'cache'       => $cache
    ];

    $app = [];

    // The application's own variables: not the engine's, not PHP's, nothing that starts
    // with _ (the SQL log and the evaluator's scratch), credentials redacted.

    foreach ( $GLOBALS as $key => $value )
      if ( padValidStore ( $key ) and ! str_starts_with ( $key, '_' )
           and ! in_array ( $key, [ 'argv', 'argc' ] ) and ! is_object ( $value ) )
        $app [$key] = padRedact ( $value, $key );

    ksort ( $app );

    $session = padRedact ( $_SESSION ?? [], '_SESSION', TRUE );

    return "\n<div id=\"padToolbar\" style=\"position:fixed;left:0;right:0;bottom:0;z-index:2147483647;"
         . "max-height:60vh;overflow:auto;background:#1d1f27;color:#e6e6e6;border-top:2px solid #7c8cff;"
         . "font:12px/1.45 ui-monospace,Menlo,Consolas,monospace;text-align:left\">"
         . "<details><summary style=\"cursor:pointer;padding:4px 10px\">" . padToolbarEsc ( $summary ) . "</summary>"
         . "<div style=\"padding:2px 10px 8px\">"
         . padToolbarPart ( 'Request',                        padToolbarLines ( $request ) )
         . padToolbarPart ( 'Tags (' . count ( $tags ) . ')', $tree )
         . padToolbarPart ( 'SQL (' . count ( $sql ) . ')',   implode ( "\n", array_map ( 'strval', $sql ) ) )
         . padToolbarPart ( 'Templates (' . count ( $files ) . ')', implode ( "\n", $files ) )
         . padToolbarPart ( 'Variables (' . count ( $app ) . ')',   padToolbarLines ( $app ) )
         . padToolbarPart ( 'Session (' . count ( $session ) . ')', padToolbarLines ( $session ) )
         . "</div></details></div>\n";

  }

  function padToolbarPart ( $title, $text ) {

    return "<details><summary style=\"cursor:pointer\">" . padToolbarEsc ( $title ) . "</summary>"
         . "<pre style=\"margin:2px 0 6px 14px;white-space:pre-wrap;color:inherit;background:none\">"
         . padToolbarEsc ( $text === '' ? '(none)' : $text ) . "</pre></details>";

  }

  function padToolbarLines ( $values ) {

    $out = '';

    foreach ( $values as $key => $value ) {

      if ( is_array ( $value ) )
        $value = json_encode ( $value, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PARTIAL_OUTPUT_ON_ERROR | JSON_INVALID_UTF8_SUBSTITUTE );
      elseif ( is_bool ( $value ) )
        $value = $value ? 'TRUE' : 'FALSE';
      elseif ( $value === NULL )
        $value = 'NULL';

      $out .= "$key = " . padMakeSafe ( (string) $value, 200 ) . "\n";

    }

    return $out;

  }

  function padToolbarEsc ( $text ) {

    return htmlspecialchars ( (string) $text, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8' );

  }

?>
