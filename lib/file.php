<?php


  function padAppData ( $file, $default='' ) {

    if ( str_starts_with ( $file, APP ) ) 
      $file = substr ( $file, strlen ( APP ) );

    $file = APP . $file;
    $dir  = substr ( $file, 0, strrpos ( $file, '/' ) );

    if     ( ! str_starts_with ( $file, '/' )       ) return padError ( "File does not start with /: $file" );
    elseif ( strpos($file, '..' ) !== FALSE         ) return padError ( "File contains ..: $file"  );
    elseif ( strpos($file, '//' ) !== FALSE         ) return padError ( "File contains //: $file"  );
    elseif ( preg_match('/[\x00-\x1F\x7F]/', $file) ) return padError ( "File contains invalid char: $file" );    
    elseif ( is_dir ($file)                         ) return padError ( "File is a directory: $file"  );
    elseif ( is_file ($dir)                         ) return padError ( "Directory is a file: $file"  );

    if ( file_exists ( $file ) )
      if ( ! is_readable ( $file ) )
        return padError ( "File is not readable: $file"  );
      else
        return file_get_contents ($file);
    else
      return $default;

  }


  function padFileGet ( $file, $default='' ) {

    if ( $file == 'php://input' )
      return file_get_contents ( 'php://input' );

    if ( ! str_starts_with($file, PAD) and ! str_starts_with($file, APP) and ! str_starts_with($file, DAT) )
      $file = PAD . $file;

    $check = padFileCheck ( $file );
    if ( $check )
      return padError ( $check );

    if ( $GLOBALS ['padInfo'] )
      include PAD . 'events/get.php';

    if ( is_dir ($file) or ! is_readable ( $file ) )
      return $default;
    else
      return file_get_contents ($file);

  }


  function padFilePut ( $file, $data='', $append=0 ) {

    if ( ! str_starts_with ( $file, DAT ) ) {

      if ( str_starts_with ( $file, APP ) ) $file = substr ( $file, strlen ( APP ) );
      if ( str_starts_with ( $file, PAD ) ) $file = substr ( $file, strlen ( PAD ) );

      $file = DAT . $file;

    }

    $check = padFileCheck ( $file );
    if ( $check )
      return padError ( $check );

    if ( $GLOBALS ['padInfo'] )
      include PAD . 'events/put.php';

    padFilePutGo ( $file, $data, $append );

  }


  function padFileCheck ( $file ) {

    if ( ! str_starts_with ( $file, '/' )       ) return "Invalid file (not starting with /): $file";
    if ( strpos($file, '..' ) !== FALSE         ) return "Invalid file (contains '..'): $file";
    if ( strpos($file, '//' ) !== FALSE         ) return "Invalid file (contains '//'): $file";
    if ( preg_match('/[\x00-\x1F\x7F]/', $file) ) return "Invalid file (contains control chars): $file";
                                                  return '';

  }



  function padAppPutFile ( $dir, $file, $data ) {

    return padAppPut ( $dir, $file, $data, 0 ); 
    
  }

  function padAppPutLine ( $dir, $file, $data ) {

    return padAppPut ( $dir, $file, $data, 1 ); 
  
   }

  function padAppPut ( $dir, $file, $data, $append ) {

    $file  = APP . "$dir/DATA/$file";
    $dir   = substr ( $file, 0, strrpos ( $file, '/' ) );

    if     ( ! str_contains($file, '/DATA/')        ) return padError ( "File does not contain /__: $file" );
    elseif ( ! str_starts_with ( $file, '/' )       ) return padError ( "File does not start with /: $file" );
    elseif ( strpos($file, '..' ) !== FALSE         ) return padError ( "File contains ..: $file"  );
    elseif ( strpos($file, '//' ) !== FALSE         ) return padError ( "File contains //: $file"  );
    elseif ( preg_match('/[\x00-\x1F\x7F]/', $file) ) return padError ( "File contains invalid char: $file" );    
    elseif ( is_dir ($file)                         ) return padError ( "File is a directory: $file"  );
    elseif ( is_file ($dir)                         ) return padError ( "Directory is a file: $file"  );
 
    padFilePutGo ( $file, $data, $append );

  }


  function padFilePutGo ( $file, $data, $append ) {

    $dir = substr ( $file, 0, strrpos ( $file, '/' ) );

    if ( ! is_writeable ( $dir ) ) {
 
      if ( file_exists ( $dir)  ) 
        return padError ( "Directory can not be written: $dir" );
   
      if ( ! mkdir ($dir, $GLOBALS ['padDirMode'], true ) )
        return padError ( "Error creating directory: $dir" );

    }

    if ( ! is_writeable ( $file ) ) {

      if ( file_exists ( $file ) ) 
        return padError ( "File can not be written: $file" );

      touch($file);
      chmod($file, $GLOBALS ['padFileMode']);

    }

    if ( is_array($data) or is_object($data) )
      $data = padJson ($data);
      
    if ( $data !== null and $data !== '' )
      if ($append) $check = file_put_contents ( $file, "$data\n", LOCK_EX | FILE_APPEND );
      else         $check = file_put_contents ( $file, $data,     LOCK_EX               );

    if ( $check === FALSE ) return padError ( "Writing to file failed: $file" );
    else                    return TRUE;
    
  }


?>