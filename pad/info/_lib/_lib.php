<?php

  function padInfoPadOccur () {

    global $pad, $padOccur;

    $return = $pad;

    $occur = $padOccur [$pad] ?? 0;

    if ( $occur <> 0 and $occur <> 99999 )
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

      if ( $file <> '.' and $file <> '..' )
        if ( is_dir ( "$dir/$file" ) )
          padInfoDelete ( "$dir/$file" );
        else
          unlink ( "$dir/$file" ) ;

    closedir ( $loop );

    rmdir ( $dir );

  }

?>
