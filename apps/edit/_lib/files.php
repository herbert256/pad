<?php

  // The files of an application as the editor sees them: listed, read, written, made,
  // copied and moved. Deleting is the trash's (store.php). Paths given here are absolute and
  // have been through editPath; these functions check what the disk says - a file that is
  // there already, a directory moved into itself.
  //
  // padFileGet and padFilePut are not used for the application's files: the one reads only
  // below pad/, apps/ and DATA/ - www/ is neither - and the other writes only below DATA/.
  // editWrite does what padFilePut does, a temporary file renamed over the old one, so a
  // reader never sees half a file.

  const editSkip = [ '.DS_Store', '.git', 'Thumbs.db' ];

  const editTextExt = [ 'pad', 'php', 'html', 'htm', 'css', 'js', 'mjs', 'json', 'xml', 'yaml',
                        'yml', 'csv', 'md', 'txt', 'sql', 'svg', 'sh', 'ini', 'env', 'curl',
                        'htaccess', 'tsx', 'jsx', 'ts', 'scss', 'less', 'conf', 'log', 'eml' ];

  const editImageExt = [ 'png' => 'image/png', 'jpg' => 'image/jpeg', 'jpeg' => 'image/jpeg',
                         'gif' => 'image/gif', 'webp' => 'image/webp', 'ico' => 'image/x-icon',
                         'svg' => 'image/svg+xml', 'avif' => 'image/avif', 'bmp' => 'image/bmp' ];

  // Every file and directory of an application, both roots, as one flat list sorted by
  // root and path - the browser builds the tree from it, and quick open searches it. The
  // framework, pad/, is listed on its own: it is the same for every application.

  function editTree ( $app, $limit = 20000 ) {

    $list = [];

    foreach ( array_keys ( editRoots ( $app ) ) as $root )
      editTreeWalk ( editRoot ( $app, $root ), '', $root, $list, $limit );

    return $list;

  }

  function editEngineTree ( $limit = 20000 ) {

    $list = [];

    editTreeWalk ( editRoot ( '', 'pad' ), '', 'pad', $list, $limit );

    return $list;

  }

  // The PHP the editor itself runs on - the framework, and the editor's own files: one of
  // them that does not parse would stop every page, the editor's too, and it could not be
  // put right from here any more. Such a save is refused; any other file is saved as it is.

  function editRunsOn ( $app, $root, $rel ) {

    if ( editExt ( $rel ) !== 'php' )
      return FALSE;

    return $root === 'pad' or ( $root === 'app' and $app === 'edit' )
        or ( $root === 'www' and $app === 'edit' );

  }

  function editTreeWalk ( $base, $rel, $root, &$list, $limit ) {

    $names = @scandir ( $base . $rel );

    if ( ! $names )
      return;

    natcasesort ( $names );

    $dirs = $files = [];

    foreach ( $names as $name ) {

      if ( $name == '.' or $name == '..' or in_array ( $name, editSkip, TRUE ) )
        continue;

      $path = $base . $rel . $name;

      if ( is_link ( $path ) and ! str_starts_with ( (string) realpath ( $path ) . '/', $base ) )
        continue;

      if ( is_dir ( $path ) )
        $dirs [] = $name;
      else
        $files [] = $name;

    }

    foreach ( $dirs as $name ) {

      if ( count ( $list ) >= $limit )
        return;

      $list [] = [ 'root' => $root, 'path' => $rel . $name, 'dir' => TRUE ];

      editTreeWalk ( $base, "$rel$name/", $root, $list, $limit );

    }

    foreach ( $files as $name ) {

      if ( count ( $list ) >= $limit )
        return;

      $path = $base . $rel . $name;

      $list [] = [ 'root'  => $root, 'path' => $rel . $name, 'dir' => FALSE,
                   'size'  => (int) @filesize ( $path ), 'mtime' => (int) @filemtime ( $path ) ];

    }

  }

  function editExt ( $path ) {

    $base = strtolower ( basename ( (string) $path ) );

    if ( $base === '' )
      return '';

    if ( $base [0] == '.' and ! str_contains ( substr ( $base, 1 ), '.' ) )
      return substr ( $base, 1 );

    $dot = strrpos ( $base, '.' );

    return $dot === FALSE ? '' : substr ( $base, $dot + 1 );

  }

  // A file as the browser gets it: its text when it is text - UTF-8 without NUL bytes - or
  // its bytes as base64 when it is an image or other binary small enough to show; sha1 and
  // mtime let a save later see whether someone else changed it in between.

  function editRead ( $file, $maxText ) {

    if ( is_dir ( $file ) )
      editFail ( 'that is a directory' );

    if ( ! is_file ( $file ) )
      editFail ( 'the file is not there (anymore)' );

    $size = (int) filesize ( $file );
    $ext  = editExt ( $file );
    $mime = editImageExt [$ext] ?? '';

    $info = [ 'size' => $size, 'mtime' => (int) filemtime ( $file ), 'writable' => is_writable ( $file ),
              'binary' => FALSE, 'image' => $mime, 'text' => NULL, 'base64' => NULL, 'sha1' => '' ];

    if ( $size > max ( $maxText, 8 * 1024 * 1024 ) ) {
      $info ['binary'] = TRUE;
      $info ['sha1']   = sha1_file ( $file );
      return $info;
    }

    $bytes = (string) file_get_contents ( $file );

    $info ['sha1'] = sha1 ( $bytes );

    $text = ( $size <= $maxText and ! str_contains ( $bytes, "\0" ) and mb_check_encoding ( $bytes, 'UTF-8' ) );

    if ( $text and ( $mime === '' or $ext == 'svg' ) )
      $info ['text'] = $bytes;
    else
      $info ['binary'] = ! $text;

    if ( $mime !== '' or ! $text )
      $info ['base64'] = base64_encode ( $bytes );

    return $info;

  }

  // Atomic: the new text goes into a temporary file beside the old one, which is renamed
  // over it. The file keeps its permissions; a new one gets the engine's $padFileMode.

  function editWrite ( $file, $text ) {

    global $padFileMode;

    $dir = dirname ( $file );

    if ( ! is_dir ( $dir ) and ! @mkdir ( $dir, 0755, TRUE ) )
      editFail ( 'the directory could not be made' );

    $mode = file_exists ( $file ) ? ( fileperms ( $file ) & 0777 ) : ( $padFileMode ?? 0644 );
    $temp = "$dir/.edit-" . bin2hex ( random_bytes ( 6 ) ) . '.tmp';

    if ( @file_put_contents ( $temp, $text ) === FALSE )
      editFail ( 'the file could not be written: ' . editLastError () );

    @chmod ( $temp, $mode );

    if ( ! @rename ( $temp, $file ) ) {
      @unlink ( $temp );
      editFail ( 'the file could not be written: ' . editLastError () );
    }

    clearstatcache ( TRUE, $file );

  }

  function editLastError () {

    $error = error_get_last ();

    return $error ? preg_replace ( '/^[a-z_]+\(.*?\): /i', '', $error ['message'] ) : 'unknown reason';

  }

  function editCreate ( $path, $text = '', $dir = FALSE ) {

    if ( file_exists ( $path ) or is_link ( $path ) )
      editFail ( "'" . basename ( $path ) . "' exists already" );

    if ( $dir ) {
      if ( ! @mkdir ( $path, 0755, TRUE ) )
        editFail ( 'the directory could not be made: ' . editLastError () );
      return;
    }

    editWrite ( $path, $text );

  }

  function editCopy ( $from, $to ) {

    if ( ! file_exists ( $from ) )
      editFail ( 'there is nothing to copy' );

    if ( file_exists ( $to ) or is_link ( $to ) )
      editFail ( "'" . basename ( $to ) . "' exists already" );

    if ( is_dir ( $from ) and str_starts_with ( "$to/", rtrim ( $from, '/' ) . '/' ) )
      editFail ( 'a directory cannot be copied into itself' );

    editCopyTree ( $from, $to );

  }

  function editCopyTree ( $from, $to ) {

    if ( ! is_dir ( $from ) ) {
      if ( ! is_dir ( dirname ( $to ) ) )
        @mkdir ( dirname ( $to ), 0755, TRUE );
      if ( ! @copy ( $from, $to ) )
        editFail ( 'the copy failed: ' . editLastError () );
      return;
    }

    if ( ! is_dir ( $to ) and ! @mkdir ( $to, 0755, TRUE ) )
      editFail ( 'the copy failed: ' . editLastError () );

    foreach ( scandir ( $from ) as $name )
      if ( $name != '.' and $name != '..' and ! is_link ( "$from/$name" ) )
        editCopyTree ( "$from/$name", "$to/$name" );

  }

  function editMove ( $from, $to ) {

    if ( ! file_exists ( $from ) )
      editFail ( 'there is nothing to move' );

    if ( file_exists ( $to ) or is_link ( $to ) )
      editFail ( "'" . basename ( $to ) . "' exists already" );

    if ( is_dir ( $from ) and str_starts_with ( "$to/", rtrim ( $from, '/' ) . '/' ) )
      editFail ( 'a directory cannot be moved into itself' );

    if ( ! is_dir ( dirname ( $to ) ) )
      @mkdir ( dirname ( $to ), 0755, TRUE );

    if ( ! @rename ( $from, $to ) )
      editFail ( 'the move failed: ' . editLastError () );

  }

  // A recursive removal of a directory the editor itself owns - a scratch directory of its
  // tests, an emptied trash item - never an application's.

  function editRemoveTree ( $path ) {

    if ( is_link ( $path ) or ! is_dir ( $path ) )
      return @unlink ( $path );

    foreach ( scandir ( $path ) as $name )
      if ( $name != '.' and $name != '..' )
        editRemoveTree ( "$path/$name" );

    return @rmdir ( $path );

  }

?>
