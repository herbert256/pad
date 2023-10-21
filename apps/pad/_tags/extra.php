<?php

  $extraFiles = [];

  if ( isset ($noExtraFiles) )
    return;
  
  $item = padTagParm ( 'item', $padOpt [$pad] [1] );
  $page = padPageGetName ($item);
  $dir  = substr($page, 0, strrpos($page, '/')   );

  $basePage = padApp . $page;
  $baseDir  = padApp . $dir;

  $html = padFileGetContents ("$basePage.pad");

  if ( strpos($html, '{staff')       !== FALSE ) $extraFiles ['staff'] ['php'] = '_data/staff.xml';
  if ( strpos($html, '{files')       !== FALSE ) $extraFiles ['staff'] ['php'] = '_data/files.json';
  if ( strpos($html, '{departments') !== FALSE ) $extraFiles ['staff'] ['php'] = '_data/departments.xml';

  $sources = padExplode ( $padContent, ',');

  $sources = array_merge ($sources, getExtra      ( $basePage )            );
  $sources = array_merge ($sources, getExtraFiles ( "$baseDir/_lib" )      );
  $sources = array_merge ($sources, getExtraFiles ( "$baseDir/_data" )     );
  $sources = array_merge ($sources, getExtraFiles ( "$baseDir/_includes" ) );
  $sources = array_merge ($sources, getExtraFiles ( "$baseDir/_content" )  );

  foreach ( $sources as $source ) {
    if     ( substr ($source, -4) == '.php')  $extraFiles [$source] ['php']   = $source;
    elseif ( substr ($source, -5) == '.pad') $extraFiles [$source] ['html']  = $source;
    else                                      $extraFiles [$source] ['other'] = $source;
  }

  if ( padExists ( "$baseDir/_inits.php"  ) ) $extraFiles ['inits'] ['php']  = "$dir/_inits.php";
  if ( padExists ( "$baseDir/_inits.pad" ) ) $extraFiles ['inits'] ['html'] = "$dir/_inits.pad";

  foreach ($extraFiles as $key => $value ) {
    if ( ! isset ( $extraFiles [$key] ['php'] )   ) $extraFiles [$key] ['php']   = '';
    if ( ! isset ( $extraFiles [$key] ['html'] )  ) $extraFiles [$key] ['html']  = '';
    if ( ! isset ( $extraFiles [$key] ['other'] ) ) $extraFiles [$key] ['other'] = '';
  }

  ksort ($extraFiles);

  return TRUE;

?>