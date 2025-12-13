<?php


  /**
   * Returns array of directory paths to search for includes.
   *
   * Builds list from current directory up to root.
   *
   * @return array Directory paths in search order.
   */
  function padDirs () {

    $padIncDirs  = padExplode ( $GLOBALS ['padDir'], '/' );
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


  /**
   * Returns directory portion of current page path.
   *
   * @return string Directory path without trailing slash.
   */
  function padDir () {

    global $padPage;

    if ( str_contains ( $padPage, '/') )
      return substr ( $padPage, 0, strrpos ($padPage, '/') );
    else
      return '';

  }


  /**
   * Returns full filesystem path to current directory.
   *
   * @return string APP path plus current directory.
   */
  function padPath () {

    global $padDir;

    if ( ! $padDir )
      return substr ( APP, -1 );
    else
      return APP . $padDir;

  }


  /**
   * Converts backslashes to forward slashes in path.
   *
   * @param string $in Input path.
   *
   * @return string Path with forward slashes.
   */
  function padCorrectPath ( $in ) {

    return str_replace ('\\',  '/', $in );

  }


  /**
   * Validates path for security (no traversal, no control chars).
   *
   * @param string $path Path to validate.
   *
   * @return bool TRUE if path is safe.
   */
  function padValidatePath ( $path ) {

    if ( $path === '' ) return FALSE;

    // Reject obvious traversal attempts
    if ( strpos($path, '..') !== FALSE ) return FALSE;

    // Reject null bytes or control characters
    if ( preg_match('/[\x00-\x1F\x7F]/', $path) ) return FALSE;

    return TRUE;

  }


  /**
   * Builds output filename from global settings.
   *
   * Combines directory, name, optional date/timestamp/uniqid, and extension.
   *
   * @param bool $withDir Include directory in path.
   *
   * @return string Complete filename.
   */
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


  /**
   * Finds data file path in _data directories.
   *
   * Checks for file with various extensions (xml, json, yaml, csv, php, curl, sql).
   *
   * @param string $check Data file name without extension.
   *
   * @return string Full path if found, empty string otherwise.
   */
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

    return '';

  }


  /**
   * Loads and parses data from a local file.
   *
   * @param string $padLocalFile Path to the data file.
   *
   * @return mixed Parsed data from file.
   */
  function padDataFileData ( $padLocalFile ) {

    return include PAD . 'types/go/local.php';

  }


  /**
   * Adds a query parameter to URL.
   *
   * @param string $url URL to modify.
   * @param string $key Parameter name.
   * @param string $val Parameter value.
   *
   * @return string URL with added parameter.
   */
  function padAddGet ($url, $key, $val ) {

    $str = ( strpos ($url, '?' ) === FALSE ) ? '?' : '&';

    return $url . $str . $key . '=' . urlencode($val);

  }


  /**
   * Appends session and request IDs to URL.
   *
   * @param string $url Input URL.
   *
   * @return string URL with padSesID and padReqID parameters.
   */
  function padAddIds ( $url ) {

    $url = padAddGet ( $url, 'padSesID', $GLOBALS ['padSesID'] );
    $url = padAddGet ( $url, 'padReqID', $GLOBALS ['padReqID'] );

    return $url;

  }


?>
