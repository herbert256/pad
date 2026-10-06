<?php

  // The fixture of upload_dir: padUpload asked to store under a directory name it does not
  // take, and what became of the file sent.

  $takeRecord = padUpload ( 'f', dir: 'not a plain name' );

  if ( $takeRecord === NULL )
    $takeResult = 'nothing sent';
  elseif ( $takeRecord === FALSE )
    $takeResult = 'refused';
  else {
    $takeResult = 'stored in ' . dirname ( $takeRecord ['file'] );
    @unlink ( $takeRecord ['path'] );
    @rmdir ( dirname ( $takeRecord ['path'] ) );
  }

?>
