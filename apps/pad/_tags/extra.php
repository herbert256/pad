<?php

  if ( isset ( $_SERVER ['HTTP_REFERER'] ) )
    if ( str_contains ( $_SERVER ['HTTP_REFERER'] , 'develop/page' ) )
      return NULL;
  
  $page = $padParm ;
  $dir  = substr($page, 0, strrpos($page, '/')   );

  $basePage = APP . $page;
  $baseDir  = APP . $dir;

  $html = padFileGetContents ("$basePage.pad");

  if ( strpos($html, '{staff')       !== FALSE ) $extraFiles ['x1'] ['php'] = '_data/staff.xml';
  if ( strpos($html, '{files')       !== FALSE ) $extraFiles ['x2'] ['php'] = '_data/files.json';
  if ( strpos($html, '{departments') !== FALSE ) $extraFiles ['x3'] ['php'] = '_data/departments.xml';

  $sources = padExplode ( $padContent, ',');

  $sources = array_merge ($sources, getExtra      ( $basePage )            );
  $sources = array_merge ($sources, getExtraFiles ( "$baseDir/_lib" )      );
  $sources = array_merge ($sources, getExtraFiles ( "$baseDir/_data" )     );
  $sources = array_merge ($sources, getExtraFiles ( "$baseDir/_includes" ) );
  $sources = array_merge ($sources, getExtraFiles ( "$baseDir/_content" )  );

  foreach ( $sources as $source ) {
    if     ( substr ($source, -4) == '.php')  $extraFiles [$source] ['php']   = $source;
    elseif ( substr ($source, -4) == '.pad')  $extraFiles [$source] ['data']  = $source;
    else                                      $extraFiles [$source] ['other'] = $source;
  }

  if ( file_exists ( "$baseDir/_inits.php" ) ) $extraFiles ['inits'] ['php']  = "$dir/_inits.php";
  if ( file_exists ( "$baseDir/_inits.pad" ) ) $extraFiles ['inits'] ['data'] = "$dir/_inits.pad";

  foreach ($extraFiles as $key => $value ) {
    if ( ! isset ( $extraFiles [$key] ['php'] )   ) $extraFiles [$key] ['php']   = '';
    if ( ! isset ( $extraFiles [$key] ['data'] )  ) $extraFiles [$key] ['data']  = '';
    if ( ! isset ( $extraFiles [$key] ['other'] ) ) $extraFiles [$key] ['other'] = '';
  }

  ksort ($extraFiles);

  return TRUE;

?>