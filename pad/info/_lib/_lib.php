<?php

  // Small helpers shared by all $padInfo report modes, loaded once by info/start/config.php
  // and info/start/tag.php.
  //
  // padInfoPadOccur  labels the current position as "level" or "level/occurrence", leaving the
  //                  inits (0) and exits (99999) sentinel occurrences off
  // padInfoGet       reads a report file, returning '' instead of failing when it is absent
  // padInfoDelete    removes a report directory and everything below it
  //
  // Only padInfoGet currently has callers; the other two are kept for report tooling.

  function padInfoPadOccur () {

    global $pad, $padOccur;

    $return = $pad;

    $occur = $padOccur [$pad] ?? 0;

    if ( $occur != 0 and $occur != 99999 )
      $return .= "/$occur";

    return $return;

  }

  function padInfoGet ( $file ) {

    if ( ! file_exists ($file) )
      return '';

    return padFileGet ($file);

  }

  function padInfoDelete ( $dir ) {

    if ( ! file_exists ( $dir ) )
      return;

    $loop = opendir ( $dir );

    while ( ( $file = readdir ( $loop ) ) !== FALSE )

      if ( $file != '.' and $file != '..' )
        if ( is_dir ( "$dir/$file" ) )
          padInfoDelete ( "$dir/$file" );
        else
          unlink ( "$dir/$file" ) ;

    closedir ( $loop );

    rmdir ( $dir );

  }

?>