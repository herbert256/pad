<?php

  // The safe-upload fixture: takes the avatar field as a PNG of at most max= bytes - 1K
  // unless the request says otherwise - says what became of it, and removes what it stored.

  $takeRecord = padUpload ( 'avatar', types: [ 'image/png' ], max: $_GET ['max'] ?? '1K' );

  if ( $takeRecord === NULL )
    $takeResult = 'nothing sent';
  elseif ( $takeRecord === FALSE )
    $takeResult = 'refused: ' . padUploadError ( 'avatar' );
  else {
    $takeResult = 'stored: '  . ( is_file ( $takeRecord ['path'] ) ? 'yes' : 'no' )
                . ', as: '    . ( preg_match ( '#^uploads/[0-9a-f]{32}\.png$#', $takeRecord ['file'] ) ? 'a random name' : $takeRecord ['file'] )
                . ', type: '  . $takeRecord ['type']
                . ', size: '  . $takeRecord ['size']
                . ', name: '  . $takeRecord ['name'];
    unlink ( $takeRecord ['path'] );
  }

?>
