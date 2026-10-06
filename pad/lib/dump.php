<?php

  // Everything the engine shows or files when a request dies: the {dump} tag, the 'dump'
  // error action, and the snapshots under DATA/dumps/<app>/.
  //
  // padDump is the entry point (padDumpTry inside an error handler, ending in padExit 500).
  // It closes any open HTML, then picks an audience: padDumpRemote for anyone but this
  // machine, who sees only the request id while the report goes to disk, and for a local
  // request padDumpConsole under the console output type and padDumpLocal - the full report
  // inline - otherwise.
  //
  // The report is assembled from single-topic collectors, each printing one block:
  // padDumpInfo (the error), padDumpStack / padDumpStackGo (backtraces, engine frames
  // shown apart from application ones), padDumpLevel / padDumpGetLevel (every level of the
  // tag stack with its tag, type, parameters, content and flags), padDumpApp, padDumpXXX
  // (globals by prefix), padDumpCurl, padDumpSQL, padDumpHeaders, padDumpRequest,
  // padDumpInput, padDumpFiles, padDumpFunctions, padDumpConstants, padDumpGlobals,
  // padDumpPhpInfo. padDumpFields sorts $GLOBALS into config, info, ids, PHP superglobals,
  // level, pad and pq groups; padDumpLines prints, with padDumpClean and padDumpShort
  // cutting values down to one readable line.
  //
  // padDumpToDir writes the same blocks as separate files (padDumpToDirGo buffers each
  // collector, padDumpFile and padDumpFilePut store them). Its Catch layers, like those in
  // lib/error.php, keep a failure while dumping from replacing the real error.

  function padDump ( $error='' ) {

    set_error_handler ( 'padErrorThrow' );

    try {

      padDumpTry ( $error );

    } catch (Throwable $e) {

      padErrorStop ( $error, $e );

    }

    restore_error_handler ();

    padExit ( 500 );

  }

  function padDumpTry ( $info ) {

    global $padOutput, $padOutputType, $padSent;

    if ( ! headers_sent () )
      header ( 'HTTP/1.0 500 Internal Server Error' );

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

    $globals = [];

    foreach ( $GLOBALS as $key => $value )
      $globals [$key] = padRedact ( $value, $key, $padDumpDeep ?? FALSE );

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

  function padDumpInput ( ) {

    padDumpLines ( 'Input', file_get_contents ('php://input') );

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

  function padDumpToDir ( $info='', $dir='' ) {

    global $padDumpToDirDone, $padLog, $padPage;

    if ( ! $dir )
      $dir = $padPage . '/' . $padLog . '-' . uniqid();

    if ( isset ( $padDumpToDirDone ) ) {

      if ( $dir !== $padDumpToDirDone )
        padDumpToDirDone ( $info, $dir, $padDumpToDirDone );

      return $padDumpToDirDone;

    }

    $padDumpToDirDone = $dir;

    set_error_handler ( 'padErrorThrow' );

    try {

      padDumpToDirGo ( $info );
      padDumpPrune ();

    } catch (Throwable $e) {

      padDumpToDirCatch ( $info, $e, $dir );

    }

    restore_error_handler ();

    return $dir;

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

    $txt = file_get_contents ('php://input') ?: '';

    if ( $txt === '' )
      return;

    $ctype = $_SERVER ['CONTENT_TYPE'] ?? '';
    $json  = json_decode ( $txt, TRUE );

    if ( str_contains ( $ctype, 'application/x-www-form-urlencoded' ) ) {

      parse_str ( $txt, $form );
      $txt  = http_build_query ( padRedact ( $form, '', TRUE ) );
      $type = 'txt';

    } elseif ( is_array ( $json ) ) {

      $txt  = padJson ( padRedact ( $json, '', TRUE ) );
      $type = 'json';

    } else {

      $txt  = strlen ( $txt ) . ' bytes of ' . ( $ctype ?: 'an unnamed type' ) . ' - not kept';
      $type = 'txt';

    }

    padDumpFilePut ( $padDumpToDirDone . "/input.$type", $txt );

  }

  // Owner only: the file 0600 and its report directory 0700, whatever $padFileMode and
  // $padDirMode give the rest of DATA.

  function padDumpFilePut ( $file, $data ) {

    global $padApp;

    padFilePut ( "dumps/$padApp/$file", $data );

    $path = DATA . "dumps/$padApp/$file";

    @chmod ( $path,            0600 );
    @chmod ( dirname ( $path ), 0700 );

  }

  // Repeatable errors filled the disk: every one a new report directory, kept forever. The
  // newest $padErrorKeep reports of this application stay, older ones are deleted.

  function padDumpPrune () {

    global $padApp, $padErrorKeep;

    $keep = (int) ( $padErrorKeep ?? 100 );
    $root = DATA . "dumps/$padApp/";

    if ( $keep < 1 or ! is_dir ( $root ) )
      return;

    // A report is a directory holding a stack.html. Found by that file, not by depth: a
    // page in a subdirectory nests its reports one level deeper per path segment.

    $reports = [];

    $files = new RecursiveIteratorIterator ( new RecursiveDirectoryIterator ( $root, FilesystemIterator::SKIP_DOTS ) );

    foreach ( $files as $file )
      if ( $file->getFilename () == 'stack.html' )
        $reports [] = $file->getPath ();

    if ( count ( $reports ) <= $keep )
      return;

    usort ( $reports, fn ( $a, $b ) => filemtime ( $b ) <=> filemtime ( $a ) );

    foreach ( array_slice ( $reports, $keep ) as $old )
      padDeleteDataDir ( $old );

  }

?>
