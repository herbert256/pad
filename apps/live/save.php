<?php

  // Run in the editor: the template, with its PHP code or its data text, becomes a page of
  // this server, saved under DATA/live/ (_lib/live.php), and the output frame goes there -
  // ?page/<id>, a request like any other, answered by www/live/index.php and page/_inits.php.
  // Asked for without a post, there is nothing to save: back to the editor.

  if ( ( $_SERVER ['REQUEST_METHOD'] ?? 'GET' ) !== 'POST' )
    padRedirect ( 'index' );

  $liveSource = (string) ( $_POST ['source'] ?? '' );
  $liveMode   = ( ( $_POST ['mode'] ?? '' ) == 'php' ) ? 'php' : 'data';
  $liveBody   = (string) ( $_POST [ $liveMode ] ?? '' );

  if ( strlen ( $liveSource ) > 65536 or strlen ( $liveBody ) > 65536 )
    padError ( 'the page is longer than the live application takes: 64 KB of template and 64 KB of PHP or data' );

  $liveId  = liveId ( $liveSource, $liveMode, $liveBody );
  $liveDir = liveDir ();

  if ( ! is_dir ( $liveDir ) )
    mkdir ( $liveDir, 0755, TRUE );

  liveSweep ();

  file_put_contents ( "$liveDir$liveId.pad", $liveSource );

  if ( $liveMode == 'php' )
    file_put_contents ( "$liveDir$liveId.php", str_starts_with ( ltrim ( $liveBody ), '<?' ) ? $liveBody : "<?php $liveBody" );
  else
    file_put_contents ( "$liveDir$liveId.data", $liveBody );

  padRedirect ( "page/$liveId" );

?>
