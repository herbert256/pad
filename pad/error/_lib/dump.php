<?php

  // The report of a request that dies (lib/dump.php): the full report shown to this machine
  // or the request id to anyone else, its single-topic collectors, and the same blocks
  // written as files under DATA/dumps/<app>/. padDump and padDumpToDir in lib/dump.php are
  // the entries, and read this file when a report is made.

  function padDumpTry ( $info ) {

    global $padOutput, $padOutputType, $padSent;

    // After the response has gone out the report goes to disk alone (padAnswerSent).

    if ( padAnswerSent () ) {
      padDumpToDir ( $info );
      return;
    }

    // The report is echoed before padExit, whose headers then find the answer sent: the 500
    // went out without the security headers every other answer has, and - but for an error
    // page - without the Cache-Control that keeps a failure out of a cache. Sent here, as
    // padRefuse sends them for a refusal (lib/exit.php).

    if ( ! headers_sent () ) {
      header ( 'HTTP/1.0 500 Internal Server Error' );
      header ( 'Cache-Control: no-cache, no-store' );
      if ( $padOutputType == 'web' )
        padSecurityHeaders ();
    }

    // A visitor gets the application's own page for a failure when it has one -
    // _errors/500.pad (error/_lib/errorPage.php) - with the request id in $id, the report on disk;
    // this machine keeps the full report. A page that fails itself leaves the plain line.

    include_once PAD . 'error/_lib/errorPage.php';

    if ( ! padLocal () and padErrorPageFile ( 500 ) ) {

      if ( ! isset ( $GLOBALS ['padDumpToDirDone'] ) )
        padDumpToDir ( $info );

      $padErrorPage = padErrorPageRender ( 500, 'Internal Server Error' );

      if ( $padErrorPage !== FALSE ) {
        padEmptyBuffers ( $padIgnored );
        if ( ! headers_sent () ) {
          header ( 'Content-Type: text/html; charset=UTF-8' );
          header ( 'Cache-Control: no-cache, no-store' );
        }
        echo $padErrorPage;
        $padSent   = TRUE;
        $padOutput = '';
        return;
      }

    }

    padEmptyBuffers ( $padIgnored );

    if ( $padOutputType == 'web' )
      for ($i = 1; $i <= 25; $i++)
          echo "</pre></div></td></tr></th></table></font></span></blockquote></h1></h2></h3></h4></h5></h6></b></i></u></p></ul></li></ol></dl></dt></dd>\r\n";

    // Whoever is not this machine gets the request id alone, whatever the output type: the
    // console report - paths, the template's source - went to a visitor of a console page.

    if     ( ! padLocal () )                padDumpRemote  ( $info );
    elseif ( $padOutputType == 'console' )  padDumpConsole ( $info );
    else                                    padDumpLocal   ( $info );

    $padSent   = TRUE;
    $padOutput = '';

  }

  function padDumpConsole ( $info ) {

    global $padDumpToDirDone;

    // The message whole, on one line: a cut at 100 characters left the engine's file and
    // line and hardly anything of what went wrong.

    echo padMakeSafe ( "Error: $info" );

    if ( $where = padSrcReport ( padErrorTemplate ( $info ) ) )
      echo "\n\n$where";

    echo "\nDir  : " . ( $padDumpToDirDone ?? padDumpToDir ( $info ) );
    echo "\n";

  }

  function padDumpLocal ( $info ) {

    padDumpFields    ( $php, $lvl, $cfg, $pad, $ids, $trc, $pq );

    echo ( "<div align=\"left\"><pre>" );

    padDumpInfo      ( $info );
    padDumpStack     ();
    padDumpLevel     ();
    padDumpInput     ();
    padDumpBuffer    ();
    padDumpApp       ();
    padDumpXXX       ( $pq, 'pq' );
    padDumpRequest   ();

    padDumpCurl      ( $pad );
    padDumpXXX       ( $pad, 'padBuild' );
    padDumpLines     ( "PAD variables",   $pad );
    padDumpLines     ( '$padInfo variables', $trc );
    padDumpLines     ( "Level variables", $lvl );
    padDumpLines     ( "ID's", $ids );
    padDumpSQL       ();
    padDumpHeaders   ();
    padDumpLines     ( 'Configuration', $cfg );

    echo ( "</pre></div>" );

  }

  function padDumpRemote ( $info ) {

    global $padDumpToDirDone;

    if ( ! isset ( $padDumpToDirDone ) )
      padDumpToDir ( $info );

    echo "Error: " . padID ();

  }

  function padDumpInfo ( $info ) {

    global $padExceptionText;

    if ( ! $info and isset ( $padExceptionText ) )
      echo ( "<hr><b>" . $padExceptionText . "</b><hr><br>" );

    if ( trim($info) )
      echo ( "<hr><b>" . htmlentities($info) . "</b><hr><br>" );

    padDumpTemplate ( $info ?: ( $padExceptionText ?? '' ) );

  }

  // Where in the template the error stands, under the message: file, line and column, the
  // lines around the spot, a near name, the wrappers, and a link that opens the editor
  // there (lib/source.php).

  function padDumpTemplate ( $info ) {

    $where = padErrorTemplate ( (string) $info );

    if ( ! $where )
      return;

    echo htmlspecialchars ( padSrcReport ( $where ), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8' );

    if ( isset ( $where ['link'] ) )
      echo '<a href="' . htmlspecialchars ( $where ['link'], ENT_QUOTES, 'UTF-8' ) . '">open in the editor</a>' . "\n";

    echo "<hr><br>";

  }

  function padDumpCurl ( &$pad ) {

    global $padCurlLast;

    if ( isset ( $padCurlLast ) ) {

      padDumpLines ( "Last Curl",  $padCurlLast );

      unset ( $pad ['padCurlLast'] );

    }

  }

  function padDumpStack () {

    global $padException, $padTryException;

    if ( isset ( $padException ) and is_object ( $padException ) ) 
      padDumpStackGo ( $padException->getTrace() );

    if ( isset ( $padTryException )  and is_object ( $padTryException ) )
      padDumpStackGo ( $padTryException->getTrace() );

    padDumpStackGo ( debug_backtrace (DEBUG_BACKTRACE_IGNORE_ARGS), 2 );
    echo "<br>";
    padDumpStackGo ( debug_backtrace (DEBUG_BACKTRACE_IGNORE_ARGS), 1 );

    return TRUE;
        
  }

  function padDumpStackGo ( $stack, $flag=0 ) {

    global $padErrorFile, $padErrorLine;

    foreach ( $stack as $key => $trace ) {

      extract ( $trace );

      $file     = $file     ?? $padErrorFile ?? '???';
      $line     = $line     ?? $padErrorLine ?? '???';
      $function = $function ?? '???';

      $error = ( str_starts_with ( $function, 'padDump' ) or str_starts_with ( $function, 'padError' ) );

      if ( $flag == 1 and ! $error ) continue;
      if ( $flag == 2 and   $error ) continue;

      if ( $file != '???' )
        echo ( "$file:$line - $function\n");

      unset ($file);
      unset ($line);
      unset ($function);

    }

  }

  function padDumpXXX (&$input, $prefix) {

    global $pad, $padOrg;

    $wrk = [];

    if ( $prefix == 'pq' )
      $wrk ['tag'] = $padOrg [  $pad ] ?? '';

    foreach ( $input as $key => $value )
      if ( str_starts_with ( $key, $prefix ) ) {
        unset ($input[$key]);
        $wrk [$key] = $value;
      }

    if ( count ($wrk) > 2 )
      padDumpLines ( $prefix, $wrk );

  }

  function padDumpSQL () {

    global $padSqlConnect, $padSqlPadConnect;

    if ( isset ( $padSqlConnect ) )
      padDumpLines ('MySQL-App', $padSqlConnect      );

    if ( isset ( $padSqlPadConnect ) )
      padDumpLines ('MySQL-pad', $padSqlPadConnect  );

  }

  function padDumpHeaders () {

    global $padHeaders;

    $out = headers_list ();
    $pad = $padHeaders ?? [];

    if ( function_exists ('getallheaders') )
      $hdr = getallheaders();
    else
      $hdr = [];

    if ( count ( $hdr ) ) padDumpLines ('Headers-in',  $hdr );
    if ( count ( $out ) ) padDumpLines ('Headers-out', $out );
    if ( count ( $pad ) ) padDumpLines ('Headers-PAD', $pad );

  }

  function padDumpRequest () {

    if ( isset ( $_REQUEST ) and count ( $_REQUEST ) )
      padDumpLines ('Request variables', $_REQUEST);

  }

  function padDumpLevel () {

    global $pad, $padCurrent;

    if ( ! isset ( $pad ) or $pad < 0 )
      return;

    for ( $lvl=$pad; $lvl>=0; $lvl-- ) {
      padDumpLines ("Level: $lvl", padDumpGetLevel ($lvl) );
      if ( isset ($padCurrent[$lvl]) )
        padDumpLines ('   Current', $padCurrent[$lvl] );
    }

  }

  function padDumpGetLevel ($pad)  {

    global $padArray, $padBase, $padElse, $padHit, $padName, $padNull, $padOpt, $padOrg, $padOut, $padPair, $padPrm, $padResult, $padTag, $padType;

    if ( ! isset($pad) or $pad === NULL or $pad < 0 )
      return [];

    return [
      'org'     => $padOrg [$pad] ?? '',
      'tag'     => $padTag [$pad] ?? '',
      'type'    => $padType [$pad] ?? '',
      'name'    => $padName [$pad] ?? '',
      'pair'    => $padPair [$pad] ?? '',
      'opt'     => $padOpt [$pad] ?? '',
      'prm'     => $padPrm [$pad] ?? '',
      'base'    => padDumpShort ($padBase[$pad]??''),
      'out'     => padDumpShort ($padOut[$pad]??''),
      'result'  => padDumpShort ($padResult[$pad]??''),
      'flags'  => [ 'null' => $padNull [$pad] ?? '',
                    'else' => $padElse [$pad] ?? '',
                    'hit' => $padHit [$pad] ?? '',
                    'Array' => $padArray [$pad] ?? '']
    ];

  }

  function padDumpGlobals ( ) {

    echo ( "\n<b>GLOBALS</b>\n");

    global $padDumpDeep;

    // Bounded as the JSON report bounds them (padClaudeFields, error/claude.php): 64 KB a
    // global, 4 MB in all, the application's own variables served first, in the order PHP
    // keeps them. Printed whole, the data that ran a request out of memory was printed again
    // in the room padBootRoom gave the report: the second fatal took the report and the
    // answer with it - an empty 500, no request id, no _errors/500.pad, for this machine too.

    $globals = [];

    foreach ( $GLOBALS as $key => $value )
      $globals [$key] = NULL;

    $room   = 4 << 20;
    $values = padRedactValues ();

    foreach ( [ FALSE, TRUE ] as $engine )
      foreach ( $GLOBALS as $key => $value ) {

        if ( $engine !== ( str_starts_with ( $key, 'pad' ) or str_starts_with ( $key, 'pq' ) or str_starts_with ( $key, '_' ) ) )
          continue;

        $left  = min ( 65536, max ( 0, $room ) );
        $own   = $left;
        $value = padClaudeBound ( $value, 0, $left, $values );
        $room -= $own - $left;

        $globals [$key] = padRedact ( $value, $key, $padDumpDeep ?? FALSE );

      }

    echo htmlentities ( print_r ( $globals, TRUE ) );

  }

  function padDumpFunctions () {

    $functions = get_defined_functions ();

    padDumpLines ( 'Functions', $functions ['user'] );

  }

  function padDumpFiles () {

    padDumpLines ( 'Included files', get_included_files () );

  }

  // Only the general and configuration parts when $all is FALSE: the environment, the
  // request variables and the server module's own section print the cookies, the
  // authorization header and whatever secrets the server environment holds, in clear.

  function padDumpPhpInfo ( $all = TRUE ) {

     if ( $all )
       phpinfo ();
     else
       phpinfo ( INFO_GENERAL | INFO_CONFIGURATION );

  }

  function padDumpClean ( &$array ) {

    foreach ( $array as $key => $value )
      if ( is_array ($value) )
         padDumpClean ( $array [$key] );
      elseif ( is_scalar ($value) )
        $array [$key] = padDumpShort ($value);

  }

  function padDumpShort ($G) {

    if ( $G === NULL)
      $G = '';

    if ( is_array ($G) )
      $G = '!!! array as input for padDumpShort !!!';

    return substr ( preg_replace('/\s+/', ' ', $G ), 0, 150 );

  }

  // The body as a report on disk keeps it (padDumpInputKept): raw, it showed the password
  // of a failed login a few lines under the request values that had it redacted.

  function padDumpInput ( ) {

    padDumpLines ( 'Input', padDumpInputKept () [0] );

  }

  function padDumpConstants ( ) {

    padDumpLines ( 'Constants', get_defined_constants () );

  }

  function padDumpBuffer ( ) {

  }

  function padDumpApp () {

    $app = [];

    foreach ( $GLOBALS as $k => $v )
      if ( padValidStore ($k) )
        $app [$k] = $v;

    ksort($app);

    padDumpLines ( 'App variables', $app );

  }

  function padDumpLines ( $info, $source ) {

    // Whatever block this is - configuration, request, headers, a level's parameters - a
    // value whose name says it is a secret is shown redacted, and cookies never in clear.
    // A report written to disk also blanks the session (padDumpToDirGo sets the flag).

    global $padDumpDeep;

    if ( is_array ( $source ) )
      $source = padRedact ( $source, '', $padDumpDeep ?? FALSE );

    if ( padSingleValue ( $source ) )
      $source = trim ( $source );

    if ( is_array ($source) and ! count($source) )
      return;
    elseif ( ! $source )
      return;

    if ( is_array($source) )
      padDumpClean ($source);

    if ($info)
      echo ( "\n<b>$info</b>\n");

    $lines = explode ( "\n", htmlentities ( print_r ( $source, TRUE ) ) );

    foreach ( $lines as $value )  {

      if ( ! trim($value)          ) continue;
      if ( trim($value) == '('     ) continue;
      if ( trim($value) == ')'     ) continue;
      if ( trim($value) == 'Array' ) continue;

      $value = str_replace ( '=&gt; Array', '', $value );

      echo "  $value\n";

    }

  }

  function padDumpFields ( &$php, &$lvl, &$cfg, &$pad, &$ids, &$inf, &$pq ) {

    $php = $lvl = $cfg = $pad = $ids = $inf = $pq = [];

    $chk1 = [ '_GET','_REQUEST','_ENV','_POST','_COOKIE','_FILES','_SERVER','_SESSION'];

    $chk3 = [ 'padPage','padSesID','padReqID','padRefID','PHPSESSID' ];

    $settings = padFileGet ( PAD . 'config/config.php' );

    foreach ($GLOBALS as $key => $value)

      if (strpos($settings, '$'.$key.' ') or strpos($settings, '$'.$key.'=') or strpos($settings, '$'.$key."\t"))

        $cfg  [$key] = $value;

      elseif ( substr($key, 0, 7)  == 'padInfo' )

        $inf [$key] = $value;

      elseif ( $key == 'padSqlConnect' )

        $ignored [$key] = 1;

      elseif ( $key == 'padSqlPadConnect' )

        $ignored [$key] = 1;

      elseif ( in_array ( $key, $chk3 ) )

        $ids [$key] = $value;

      elseif ( in_array ( $key, $chk1 ) )

        $php [$key] = $value;

      elseif ( in_array ( $key, padLevelVars ) ) {

        if ( isset($value[0]) and ! $value[0] )
          unset ($value[0]);

        $lvl [$key] = $value;

      } elseif ( substr($key, 0, 3)  == 'pad' )

        $pad [$key] = $value;

       elseif ( substr($key, 0, 2)  == 'pq' )

        $pq [$key] = $value;

    ksort($inf);
    ksort($cfg);
    ksort($php);
    ksort($lvl);
    ksort($pad);

  }

  function padDumpToDirDone ( $info, $dir, $done ) {

    set_error_handler ( 'padErrorThrow' );

    try {

      padDumpFilePut ( "$dir/error.txt", "$info\n\n$done" );

    } catch (Throwable $e ) {

    }

    restore_error_handler ();

  }

  // A report on disk outlives the request and is read by whoever can read DATA: on top of
  // the redaction every report has, it blanks the session values ($padDumpDeep), keeps a
  // request body only as redacted form or JSON fields, and leaves the environment and the
  // request variables out of phpinfo.

  function padDumpToDirGo ( $info ) {

    global $padDumpDeep;

    $padDumpDeep = TRUE;

    if ( $info ) {
      ob_start ();
      padDumpInfo ( $info );
      padDumpFile ( '_ERROR', ob_get_clean () );
    }

    ob_start (); padDumpStack     ();                 padDumpFile ( 'stack',     ob_get_clean () );
    ob_start (); padDumpBuffer    ();                 padDumpFile ( 'buffer',    ob_get_clean () );
    ob_start (); padDumpRequest   ();                 padDumpFile ( 'request',   ob_get_clean () );
    ob_start (); padDumpSQL       ();                 padDumpFile ( 'sql',       ob_get_clean () );
    ob_start (); padDumpHeaders   ();                 padDumpFile ( 'headers',   ob_get_clean () );
    ob_start (); padDumpPhpInfo   ( FALSE );          padDumpFile ( 'phpinfo',   ob_get_clean () );
    ob_start (); padDumpLevel     ();                 padDumpFile ( 'tree',      ob_get_clean () );
    ob_start (); padDumpFiles     ();                 padDumpFile ( 'files',     ob_get_clean () );
    ob_start (); padDumpFunctions ();                 padDumpFile ( 'functions', ob_get_clean () );
    #ob_start (); padDumpConstants ();                 padDumpFile ( 'constants', ob_get_clean () );
    ob_start (); padDumpApp       ();                 padDumpFile ( 'app',       ob_get_clean () );

    padDumpFields ( $php, $lvl, $cfg, $pad, $ids, $inf, $pq );

    ob_start (); padDumpLines     ( "ID's", $ids );   padDumpFile ( 'ids',       ob_get_clean () );
    ob_start (); padDumpCurl      ( $pad );           padDumpFile ( 'curl',      ob_get_clean () );
    ob_start (); padDumpXXX       ( $pq, 'pq' );      padDumpFile ( 'sequence',  ob_get_clean () );
    ob_start (); padDumpLines     ( "Info", $inf );   padDumpFile ( 'info',      ob_get_clean () );
    ob_start (); padDumpLines     ( "Level", $lvl );  padDumpFile ( 'level',     ob_get_clean () );
    ob_start (); padDumpLines     ( 'Config', $cfg ); padDumpFile ( 'config',    ob_get_clean () );
    ob_start (); padDumpLines     ( 'PHP', $php );    padDumpFile ( 'php',       ob_get_clean () );
    ob_start (); padDumpLines     ( "PAD",   $pad );  padDumpFile ( 'pad',       ob_get_clean () );

    ob_start (); padDumpGlobals   ();                 padDumpFile ( 'globals',   ob_get_clean () );

    padDumpInputToFile () ;

  }

  function padDumpToDirCatch ( $info, $e, $dir ) {

    set_error_handler ( 'padErrorThrow' );

    try {

      padDumpFilePut ( "$dir/oops.txt",
                           "$info\n\n" .
                           $e->getFile() . ':' . $e->getLine() . ' ' . $e->getMessage()
                         );

    } catch (Throwable $e2) {

      padDumpToDirCatchCatch ( $info, $e, $e2 );

    }

    restore_error_handler ();

  }

  function padDumpToDirCatchCatch ( $info, $e1, $e2 ) {

    set_error_handler ( 'padErrorThrow' );

    try {

      gc_collect_cycles();

      padLogError ( 'DIR-CATCH: ' . $info );
      padLogError ( $e1->getFile() . ':' . $e1->getLine()  . ' DIR-CATCH: ' . $e1->getMessage() );
      padLogError ( $e2->getFile() . ':' . $e2->getLine()  . ' DIR-CATCH: ' . $e2->getMessage() );

    } catch (Throwable $e2) {

    }

    restore_error_handler ();

  }

  function padDumpFile ( $file, $txt ) {

    global $padDumpToDirDone;

    $dir = $padDumpToDirDone;
    $txt = trim ( $txt );

    padDumpFilePut ( "$dir/$file.html", "<pre>$txt</pre>" );

  }

  // The raw body of a failed login is its password. A form or JSON body is kept with its
  // secret-named fields redacted; any other body only by its size and type.

  function padDumpInputToFile () {

    global $padDumpToDirDone;

    [ $txt, $type ] = padDumpInputKept ();

    if ( $txt === '' )
      return;

    padDumpFilePut ( $padDumpToDirDone . "/input.$type", $txt );

  }

?>
