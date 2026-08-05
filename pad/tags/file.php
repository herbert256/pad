<?php

  // {file dir=.. name=.. ext=..}...{/file} writes its content to a file instead of to the
  // page. The first visit only asks for a second one at level end ($padWalk = 'end'),
  // because the content has to be rendered first; the dir, name, ext, date, stamp and id
  // parameters are put in the globals padFileName() builds the path from, padFilePut()
  // writes it, and clearing $padContent keeps the text out of the output.

  if ( $padWalk [$pad] == 'start' ) {
    $padWalk [$pad] = 'end';
    return TRUE;
  }

  $padFileDir        = padTagParm ( 'dir',   ''     );
  $padFileName       = padTagParm ( 'name',  'file' );
  $padFileExtension  = padTagParm ( 'ext',   'ext'  );
  $padFileDate       = padTagParm ( 'date',  ''     );
  $padFileTimeStamp  = padTagParm ( 'stamp', ''     );
  $padFileUniqId     = padTagParm ( 'id',    ''     );

  padFilePut ( padFileName (),  $padContent );

  $padContent = '';

  return TRUE;

?>