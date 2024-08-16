<?php

  $padFilesDir           = padTagParm ('dir', $padParm);
  $padFilesMask          = padTagParm ('mask');
  $padFilesOnlyFiles     = padTagParm ('onlyFiles');
  $padFilesOnlyDirs      = padTagParm ('onlyDirs');
  $padFilesRecursive     = padTagParm ('recursive');
  $padFilesExclude       = padTagParm ('exclude');
  $padFilesIncludeHidden = padTagParm ('includeHidden');
  $padFilesBase          = padTagParm ('base');
  $padFilesGroup         = padTagParm ('group');


  if     ( $padFilesBase == 'app'  ) $padFilesScan = '/app/'  . "/$padFilesDir";
  elseif ( $padFilesBase == 'data' ) $padFilesScan = '/data/' . "/$padFilesDir";
  elseif ( $padFilesBase == 'pad'  ) $padFilesScan = '/pad/'   . "/$padFilesDir";
  else                               $padFilesScan = "/$padFilesDir";

  $padFilesScan = str_replace ( '//', '/', $padFilesScan);

  if ( $padFilesRecursive ) {
    $padFilesDirectory = new RecursiveDirectoryIterator ( $padFilesScan );
    $padFilesIterator  = new RecursiveIteratorIterator  ( $padFilesDirectory );
  } else {
    $padFilesDirectory = new DirectoryIterator ( $padFilesScan      );
    $padFilesIterator  = new IteratorIterator  ( $padFilesDirectory );
  }

  $padFilesArray = [];

  foreach ( $padFilesIterator as $padFilesFile ) {

    $padFilesPath = $padFilesFile->getPathname();
    $padFilesName = $padFilesFile->getFilename();
    $padFilesExt  = $padFilesFile->getExtension();

    if ( $padFilesOnlyFiles       and ! $padFilesFile->isFile()                     ) continue;
    if ( $padFilesOnlyDirs        and ! $padFilesFile->isDir()                      ) continue;
    if ( $padFilesMask            and ! fnmatch ( $padFilesMask, $padFilesName    ) ) continue;
    if ( $padFilesExclude         and   fnmatch ( $padFilesExclude, $padFilesName ) ) continue;
    if ( ! $padFilesIncludeHidden and   $padFilesName [0] == '.'                    ) continue;

    $padFiles ['path']  = $padFilesFile->getPathname();
    $padFiles ['file']  = $padFilesFile->getFilename();
    $padFiles ['ext']   = $padFilesFile->getExtension();

    $padFiles ['item']  = str_replace ( $padFilesScan, '', $padFiles ['path'] );

    if ( $padFilesFile->isFile() )
      $padFiles ['item']  = substr ( $padFiles ['item'], 0+1, strrpos($padFiles ['item'], '.')-1 );
    
    $padFiles ['dir']   = substr ( $padFiles ['item'], 0, strrpos($padFiles ['item'], '/')   );

    if ( $padFilesGroup )
      $padFilesArray [ $padFiles ['item'] ] = $padFiles;
    else
      $padFilesArray [] = $padFiles;

  }

  return $padFilesArray;

?>