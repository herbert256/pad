<?php

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

  padWriteFile ( $padContent );

  $padContent = '';

  return TRUE;
 
?>