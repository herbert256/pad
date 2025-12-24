<?php

  function getPath ( $file ) {

    $file = realpath ( $file );

    if ( $file === FALSE )
      return FALSE;

    return str_replace ('\\',  '/', $file );

  }

  function fileGet ( $file, $default='' ) {

    if ( ! str_starts_with ( $file, APP ) )
      $file = APP . $file;

    $file = getPath ( $file );

    if ( $file === FALSE )
      return $default;

    if ( ! str_starts_with ( $file, APP ) ) return $default;
    if (   is_dir          ( $file      ) ) return $default;
    if ( ! file_exists     ( $file      ) ) return $default;
    if ( ! is_readable     ( $file      ) ) return $default;

    $contents = file_get_contents ($file);

    return ($contents === false) ? $default : $contents;

  }

  function filePut ( $dir, $file, $data, $append=0 ) {

    if ( ! str_starts_with ( $dir, APP ) )
      $dir = APP . $dir;

    $dir  = rtrim($dir, '/');
    $file = "$dir/DATA/$file";

    if ( ! realpath ( $file ) and ! fileNew ( $file ) )
      return FALSE;

    $file = getPath ( $file );

    if ( $file === FALSE                       ) return FALSE;
    if ( ! str_starts_with ( $file, APP      ) ) return FALSE;
    if ( ! str_contains    ( $file, '/DATA/' ) ) return FALSE;
    if ( ! file_exists     ( $file           ) ) return FALSE;
    if ( ! is_writeable    ( $file           ) ) return FALSE;

    if ( is_array($data) or is_object($data) or is_resource ($data) )
      $data = padJson ($data);

    if ( $data === null or $data === TRUE or$data === FALSE or trim($data) === '' )
      return FALSE;

    if ($append) $check = file_put_contents ( $file, "$data\n", LOCK_EX | FILE_APPEND );
    else         $check = file_put_contents ( $file, $data,     LOCK_EX               );

    if ( $check === FALSE )
      return FALSE;

    return TRUE;

  }

  function fileNew ( $file ) {

    global $padDirMode, $padFileMode;

    if ( str_ends_with ( $file, '/' ) )
      return FALSE;

    $file = str_replace ( '\\',  '/', $file );
    $dir  = dirname ( $file );

    if ( ! str_starts_with ( $file, APP )                 ) return FALSE;
    if ( $dir  == '/'                                     ) return FALSE;
    if ( is_dir ($file)                                   ) return FALSE;
    if ( is_file ($dir)                                   ) return FALSE;
    if ( strpos($file, '..'     ) !== FALSE               ) return FALSE;
    if ( strpos($file, ','      ) !== FALSE               ) return FALSE;
    if ( strpos($file, ':'      ) !== FALSE               ) return FALSE;
    if ( strpos($file, '//'     ) !== FALSE               ) return FALSE;
    if ( strpos($file, '/DATA/' ) === FALSE               ) return FALSE;
    if ( preg_match('/[\x00-\x1F\x7F]/', $file)           ) return FALSE;

    if ( file_exists ( $dir ) ) {

      if ( ! is_dir       ( $dir ) ) return FALSE;
      if ( ! is_writeable ( $dir ) ) return FALSE;

    } else {

      if ( ! mkdir ($dir, $padDirMode, true ) )
        return FALSE;

    }

    if ( ! touch($file)                                 ) return FALSE;
    if ( ! chmod($file, $padFileMode)       ) return FALSE;

    return TRUE;

  }

  function filePutFile ( $dir, $file, $data ) {

    return filePut ( $dir, $file, $data, 0 );

  }

  function filePutLine ( $dir, $file, $data ) {

    return filePut ( $dir, $file, $data, 1 );

   }

  function fileDeleteDir ( $dir ) {

    if ( $dir === '' )
      return FALSE;

    if ( ! str_starts_with ( $dir, APP ) )
      $dir = APP . $dir;

    return fileDeleteDirGo ( $dir . '/DATA/' );

  }

  function fileDeleteDirGo ( $dir ) {

    $dir = getPath ( $dir );

    if ( $dir === FALSE )
      return FALSE;

    if ( ! str_ends_with ( $dir, '/' ) )
      $dir .= '/';

    if ( ! file_exists     ( $dir           ) ) return FALSE;
    if ( ! str_starts_with ( $dir, APP      ) ) return FALSE;
    if ( ! str_contains    ( $dir, '/DATA/' ) ) return FALSE;

    foreach ( array_diff ( scandir ( $dir ), [ '.', '..' ] ) as $file ) {

      if ( is_dir ( "$dir/$file" ) and ! is_link ( "$dir/$file" ) ) {

        if ( ! fileDeleteDirGo ( "$dir/$file" ) )
          return FALSE;

      } else

        if ( ! unlink ( "$dir/$file" ) )
          return FALSE;

    }

    if ( ! rmdir ( $dir ) )
      return FALSE;

    return TRUE;

  }

?>