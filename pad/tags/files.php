<?php

  // {files 'dir', mask='*.pad', recursive} scans a directory and returns it as this
  // level's data, one occurrence per entry with the fields path, file, ext, item and dir.
  // item is the entry relative to the scanned directory, with its extension taken off for
  // files, so it is the name a page or template is addressed by; dir is the part of item
  // before the last /.
  //
  // base= says what the directory is relative to: app for APP, data for DATA, pad for the
  // path as given, and anything else for the filesystem root - and whatever the base, the
  // directory must lie inside the applications, the engine or DATA. recursive picks the
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

  // A trailing / comes off: the item is cut from the path behind the scanned directory and
  // its separator, and with 'dir/' that cut took the first letter of every name - README
  // became EADME, sub/b became ub/b.

  $padFilesScan = rtrim ( str_replace ( '//', '/', $padFilesScan), '/' ) ?: '/';

  // A directory that is not there was a raw iterator exception. Strict mode names it;
  // the lenient walk answers the empty list a scan of nothing is.

  if ( ! is_dir ( $padFilesScan ) ) {

    if ( $padCheckSyntax )
      padError ( "there is no directory named '$padFilesDir' for {files}" );

    return [];

  }

  if ( ! padDirContained ( $padFilesScan ) ) {
    padError ( "the directory '$padFilesDir' lies outside the applications, the engine and DATA" );
    return [];
  }

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

    // . and .. are no entries of the directory but its references to itself and its
    // parent; with includeHidden they were listed, one pair for every directory of a
    // recursive scan.

    if ( $padFilesName == '.' or $padFilesName == '..' ) continue;

    if ( $padFilesOnlyFiles       and ! $padFilesFile->isFile()                     ) continue;
    if ( $padFilesOnlyDirs        and ! $padFilesFile->isDir()                      ) continue;
    if ( $padFilesMask            and ! fnmatch ( $padFilesMask, $padFilesName    ) ) continue;
    if ( $padFilesExclude         and   fnmatch ( $padFilesExclude, $padFilesName ) ) continue;
    if ( ! $padFilesIncludeHidden and   $padFilesName [0] == '.'                    ) continue;

    $padFiles ['path']  = $padFilesFile->getPathname();
    $padFiles ['file']  = $padFilesFile->getFilename();
    $padFiles ['ext']   = $padFilesFile->getExtension();

    // The item is the path behind the scanned directory and its separator, for a file and a
    // directory alike - the / was taken off the files only, so a directory came as /sub
    // where a file came as a.

    $padFiles ['item']  = $padFiles ['path'];

    if ( str_starts_with ( $padFiles ['item'], rtrim ( $padFilesScan, '/' ) . '/' ) )
      $padFiles ['item'] = substr ( $padFiles ['item'], strlen ( rtrim ( $padFilesScan, '/' ) ) + 1 );

    // The extension comes off by its own length: cut at the last dot, a file without one
    // lost its last letter (README became READM) and a dot in a directory name cut the
    // path there (v1.2/README became v1).

    if ( $padFilesFile->isFile() and $padFiles ['ext'] !== '' )
      $padFiles ['item'] = substr ( $padFiles ['item'], 0, - strlen ( $padFiles ['ext'] ) - 1 );

    $padFiles ['dir']   = substr ( $padFiles ['item'], 0, strrpos($padFiles ['item'], '/')   );

    if ( $padFilesGroup )
      $padFilesArray [ $padFiles ['item'] ] = $padFiles;
    else
      $padFilesArray [] = $padFiles;

  }

  return $padFilesArray;

?>
