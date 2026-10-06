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

  // The globals are the file writer's as well - the name a page with the file or download
  // output type is written or sent under - so they are put back once this file is named.
  // They were left as the tag set them, and such a page was then written over the tag's
  // file, under the tag's name.

  $padFileKeep = [ $padFileDir, $padFileName, $padFileExtension, $padFileDate, $padFileTimeStamp, $padFileUniqId ];

  $padFileDir        = padTagParm ( 'dir',   ''     );
  $padFileName       = padTagParm ( 'name',  'file' );
  $padFileExtension  = padTagParm ( 'ext',   'ext'  );
  $padFileDate       = padTagParm ( 'date',  ''     );
  $padFileTimeStamp  = padTagParm ( 'stamp', ''     );
  $padFileUniqId     = padTagParm ( 'id',    ''     );

  $padFileNamed = padFileName ();

  [ $padFileDir, $padFileName, $padFileExtension, $padFileDate, $padFileTimeStamp, $padFileUniqId ] = $padFileKeep;

  padFilePut ( $padFileNamed, $padContent );

  $padContent = '';

  return TRUE;

?>
