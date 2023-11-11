<?php


  function padDumpToDir ( $info='', $dir='' ) {

    if ( isset ( $GLOBALS ['padDumpToDir'] ) )
      return;
 
    $GLOBALS ['padDumpToDir'] = TRUE;

    if ( ! $dir )
      $dir = "dumps/" . $GLOBALS ['padPage'] . '/' . $GLOBALS ['padReqID'] . '-' . padRandomString();

    set_error_handler ( 'padErrorThrow' );

    try {

      padDumpToDirGo ( $info, $dir );

      unset ( $GLOBALS ['padDumpToDir'] );

    } catch (Throwable $e) {

      padDumpToDirCatch ( $info, $e, $dir );

    }

    restore_error_handler (); 

    return $dir;

  }


  function padDumpToDirGo ( $info, $dir ) {

    padDumpFields ( $php, $lvl, $app, $cfg, $pad, $ids, $trc, $err );

    ob_start (); padDumpInfo      ( $info );          padDumpFile ( 'info',        ob_get_clean (), $dir );
    ob_start (); padDumpErrors    ();                 padDumpFile ( 'error',       ob_get_clean (), $dir );
    ob_start (); padDumpLines     ( "Error", $err );  padDumpFile ( 'error-extra', ob_get_clean (), $dir );
    ob_start (); padDumpStack     ();                 padDumpFile ( 'stack',       ob_get_clean (), $dir );
    ob_start (); padDumpLines     ( "ID's", $ids );   padDumpFile ( 'ids',         ob_get_clean (), $dir );
    ob_start (); padDumpLevel     ();                 padDumpFile ( 'level',       ob_get_clean (), $dir );
    ob_start (); padDumpRequest   ();                 padDumpFile ( 'request',     ob_get_clean (), $dir );
    ob_start (); padDumpLines     ( "App", $app );    padDumpFile ( 'app-vars',    ob_get_clean (), $dir );
    ob_start (); padDumpCurl      ( $pad );           padDumpFile ( 'last-curl',   ob_get_clean (), $dir );
    ob_start (); padDumpXXX       ( $pad, 'padSeq' ); padDumpFile ( 'sequence',    ob_get_clean (), $dir );
    ob_start (); padDumpFiles     ();                 padDumpFile ( 'files',       ob_get_clean (), $dir );
    ob_start (); padDumpFunctions ();                 padDumpFile ( 'functions',   ob_get_clean (), $dir );
    ob_start (); padDumpLines     ( "PAD",   $pad );  padDumpFile ( 'pad-vars',    ob_get_clean (), $dir );
    ob_start (); padDumpLines     ( "Trace", $trc );  padDumpFile ( 'trace',       ob_get_clean (), $dir );
    ob_start (); padDumpLines     ( "Level", $lvl );  padDumpFile ( 'level-vars',  ob_get_clean (), $dir );
    ob_start (); padDumpSQL       ();                 padDumpFile ( 'sql',         ob_get_clean (), $dir );
    ob_start (); padDumpHeaders   ();                 padDumpFile ( 'headers',     ob_get_clean (), $dir );
    ob_start (); padDumpLines     ( 'Config', $cfg ); padDumpFile ( 'config',      ob_get_clean (), $dir );
    ob_start (); padDumpLines     ( 'PHP', $php );    padDumpFile ( 'php-vars',    ob_get_clean (), $dir );
    ob_start (); padDumpPhpInfo   ();                 padDumpFile ( 'php-info',    ob_get_clean (), $dir );
    ob_start (); padDumpGlobals   ();                 padDumpFile ( 'globals',     ob_get_clean (), $dir );

    padDumpInputToFile ( 'input', $dir ) ;

  }


  function padDumpToDirCatch ( $info, $e, $dir ) {

    set_error_handler ( 'padErrorThrow' );

    try {

      padFilePutContents ( "$dir/oops.txt", 
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

      padErrorLog ( 'DIR-CATCH: ' . $info );
      padErrorLog ( $e1->getFile() . ':' . $e1->getLine()  . ' DIR-CATCH: ' . $e1->getMessage() );
      padErrorLog ( $e2->getFile() . ':' . $e2->getLine()  . ' DIR-CATCH: ' . $e2->getMessage() );

    } catch (Throwable $e2) {

      // giving up
  
    }

    restore_error_handler ();

  }  


  function padDumpFile ( $file, $txt, $dir ) {

    $txt = trim ( $txt );

    if ( $txt )
      padFilePutContents ( "$dir/$file.html", "<pre>$txt</pre>" );

  }


  function padDumpInputToFile ( $file, $dir ) {

    $txt = trim ( file_get_contents('php://input') ?? '' );
    
    if ( ! $txt )
      return;

    $type = padContentType ( $txt );

    if ( $type == 'csv' )
      $type = 'txt';

    padFilePutContents ( "$dir/$file.$type", "<pre>$txt</pre>" );

  }


?>