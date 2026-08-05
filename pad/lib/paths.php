<?php

  // Directory, file name and URL construction.
  //
  // padDirs          the search order behind directory inheritance: the current page's
  //                  directory first, then each parent, ending at the application root.
  //                  Every _tags/, _include/, _data/ ... lookup walks this list
  // padDir           the directory part of $padPage; padPath is APP plus $padDir
  // padCorrectPath   normalises Windows backslashes
  // padValidatePath  rejects empty paths, .. and control characters
  // padFileName      assembles an output file name from the {file} tag globals - name,
  //                  optional directory, optional date, timestamp and random suffix,
  //                  then the extension
  // padDataFileName  finds a _data/ file by bare name, trying the known extensions in
  //                  turn (xml, json, yaml, csv, php, curl, sql) up the padDirs chain and
  //                  finally in the _common application; returns '' when nothing matches
  // padDataFileData  loads such a file through types/_go/local.php
  // padAddGet        appends one urlencoded key=value, picking ? or & as needed
  // padAddIds        appends the session and request ids, so links keep the request chain

  function padDirs () {

    global $padDir;

    $padIncDirs  = padExplode ( $padDir, '/' );
    $padIncDir   = '';
    $padIncCheck = [];

    foreach ( $padIncDirs as $padK => $padV ) {
      $padIncDir .= "$padV/";
      $padIncCheck [] = '/' . $padIncDir;
    }

    $padIncCheck    = array_reverse ($padIncCheck);
    $padIncCheck [] = '/';

    return $padIncCheck;

  }

  function padDir () {

    global $padPage;

    if ( str_contains ( $padPage, '/') )
      return substr ( $padPage, 0, strrpos ($padPage, '/') );
    else
      return '';

  }

  function padPath () {

    global $padDir;

    if ( ! $padDir )
      return substr ( APP, -1 );
    else
      return APP . $padDir;

  }

  function padCorrectPath ( $in ) {

    return str_replace ('\\',  '/', $in );

  }

  function padValidatePath ( $path ) {

    if ( $path === '' ) return FALSE;

    if ( strpos($path, '..') !== FALSE ) return FALSE;

    if ( preg_match('/[\x00-\x1F\x7F]/', $path) ) return FALSE;

    return TRUE;

  }

  function padFileName ( $withDir = TRUE) {

    global $padFileDir, $padFileName, $padFileDate, $padFileTimeStamp, $padFileUniqId, $padFileExtension;

    if ( $withDir and $padFileDir )
      $name = "$padFileDir/$padFileName";
    else
      $name = $padFileName;

    if ( $padFileDate )
      $name .= '_' . date ('Y-m-d');

    if ( $padFileTimeStamp )
      $name .= '_' . padTimeStamp ();

    if ( $padFileUniqId )
      $name .= '_' . padRandomString ( $padFileUniqId );

    $name .= '.' . $padFileExtension;

    return $name;

  }

  function padDataFileName ( $check ) {

    foreach ( padDirs () as $key => $value ) {

      $file = APP2 . $value . "_data/$check";

      if ( file_exists ( $file ) and ! is_dir ( $file ) ) return  $file;
      if ( file_exists ( "$file.xml"  )                 ) return "$file.xml";
      if ( file_exists ( "$file.json" )                 ) return "$file.json";
      if ( file_exists ( "$file.yaml" )                 ) return "$file.yaml";
      if ( file_exists ( "$file.csv"  )                 ) return "$file.csv";
      if ( file_exists ( "$file.php"  )                 ) return "$file.php";
      if ( file_exists ( "$file.curl" )                 ) return "$file.curl";
      if ( file_exists ( "$file.sql"  )                 ) return "$file.sql";

    } 

    $file = COMMON . "_data/$check";

    if ( file_exists ( $file ) and ! is_dir ( $file ) ) return  $file;
    if ( file_exists ( "$file.xml"  )                 ) return "$file.xml";
    if ( file_exists ( "$file.json" )                 ) return "$file.json";
    if ( file_exists ( "$file.yaml" )                 ) return "$file.yaml";
    if ( file_exists ( "$file.csv"  )                 ) return "$file.csv";
    if ( file_exists ( "$file.php"  )                 ) return "$file.php";
    if ( file_exists ( "$file.curl" )                 ) return "$file.curl";
    if ( file_exists ( "$file.sql"  )                 ) return "$file.sql";

    return '';

  }

  function padDataFileData ( $padLocalFile ) {

    return include PAD . 'types/_go/local.php';

  }

  function padAddGet ($url, $key, $val ) {

    $str = ( strpos ($url, '?' ) === FALSE ) ? '?' : '&';

    return $url . $str . $key . '=' . urlencode($val);

  }

  function padAddIds ( $url ) {

    global $padReqID, $padSesID;

    $url = padAddGet ( $url, 'padSesID', $padSesID );
    $url = padAddGet ( $url, 'padReqID', $padReqID );

    return $url;

  }

?>