<?php

  // {files 'dir', mask='*.pad', recursive} scans a directory and returns it as this
  // level's data, one occurrence per entry with the fields path, file, ext, item and dir.
  // item is the entry relative to the scanned directory, with its extension taken off for
  // files, so it is the name a page or template is addressed by; dir is the part of item
  // before the last /.
  //
  // base= says what the directory is relative to: app for APP, data for DATA, pad for the
  // path as given, and anything else for the filesystem root. recursive picks the
  // recursive iterator; mask, exclude, onlyFiles, onlyDirs and includeHidden drop entries
  // as they come by; group makes item the array key instead of a running number.

  $padFilesDir           = padTagParm ('dir', $padParm);
  $padFilesMask          = padTagParm ('mask');
  $padFilesOnlyFiles     = padTagParm ('onlyFiles');
  $padFilesOnlyDirs      = padTagParm ('onlyDirs');
  $padFilesRecursive     = padTagParm ('recursive');
  $padFilesExclude       = padTagParm ('exclude');
  $padFilesIncludeHidden = padTagParm ('includeHidden');
  $padFilesBase          = padTagParm ('base');
  $padFilesGroup         = padTagParm ('group');

  if     ( $padFilesBase == 'app'  ) $padFilesScan = APP . "$padFilesDir";
  elseif ( $padFilesBase == 'data' ) $padFilesScan = DATA . "$padFilesDir";
  elseif ( $padFilesBase == 'pad'  ) $padFilesScan = "$padFilesDir";
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

    $padFilesName = $padFilesFile->getFilename();

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