<?php


  function padFileGet ( $file, $default='' ) {

    global $padInfo;

    if ( $file == 'php://input' )
      return file_get_contents ( 'php://input' );

    if ( ! str_starts_with($file, PAD) and 
         ! str_starts_with($file, APPS) and 
         ! str_starts_with($file, DAT) )
      $file = PAD . $file;

    $check = padFileCheck ( $file );
    if ( $check )
      return $default;

    if ( ! file_exists ( $file ) )
      return $default;

    if ( $padInfo )
      include PAD . 'events/get.php';

    if ( is_dir ($file) or ! is_readable ( $file ) )
      return $default;
    else
      return file_get_contents ($file);

  }


  function padFilePut ( $file, $data='', $append=0 ) {

    global $padInfo, $padDirMode, $padFileMode;

    if ( ! str_starts_with ( $file, DAT ) )
      $file = DAT . $file;

    $check = padFileCheck ( $file );
    if ( $check )
      return padError ( $check );

    if ( $padInfo )
      include PAD . 'events/put.php';

    $dir = substr ( $file, 0, strrpos ( $file, '/' ) );

    if ( ! is_writeable ( $dir ) ) {

      if ( file_exists ( $dir)  )
        return padError ( "Directory can not be written: $dir" );

      if ( ! mkdir ($dir, $padDirMode, true ) )
        return padError ( "Error creating directory: $dir" );

    }

    if ( ! is_writeable ( $file ) ) {

      if ( file_exists ( $file ) )
        return padError ( "File can not be written: $file" );

      touch($file);
      chmod($file, $padFileMode);

    }

    if ( is_array($data) or is_object($data) )
      $data = padJson ($data);

    if ( $data !== null and $data !== '' ) {

      if ($append) $check = file_put_contents ( $file, "$data\n", LOCK_EX | FILE_APPEND );
      else         $check = file_put_contents ( $file, $data,     LOCK_EX               );

      if ( $check === FALSE )
        return padError ( "Writing to file failed: $file" );

    }

    return TRUE;

  }


  function padFileCheck ( $file ) {

    if ( ! str_starts_with ( $file, '/' )       ) return "Invalid file (not starting with /): $file";
    if ( strpos($file, '..' ) !== FALSE         ) return "Invalid file (contains '..'): $file";
    if ( strpos($file, '//' ) !== FALSE         ) return "Invalid file (contains '//'): $file";
    if ( preg_match('/[\x00-\x1F\x7F]/', $file) ) return "Invalid file (contains control chars): $file";
                                                  return '';

  } 


  function padDeleteDataDir ( $dir ) {

    $dir = getPath ( $dir );

    if ( $dir === FALSE )
      return;

    if ( ! str_ends_with ( $dir, '/' ) )
      $dir .= '/';

    if ( ! file_exists     ( $dir           ) ) return;
    if ( ! is_dir          ( $dir           ) ) return;
    if ( ! str_starts_with ( $dir, DAT      ) ) return;

    foreach ( padFiles ( $dir ) as $file )
      if ( is_dir ( "$dir/$file" ) and ! is_link ( "$dir/$file" ) )
        padDeleteDataDir ( "$dir/$file" );
      else
        unlink ( "$dir/$file" );

     rmdir ( $dir );

  }


  function files ( $dir ) {

    return array_diff ( scandir ( $dir ), [ '.', '..' ] );

  }

?>