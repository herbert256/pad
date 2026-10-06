<?php

  // Directory, file name and URL construction.
  //
  // padDirs          the search order behind directory inheritance: the current page's
  //                  directory first, then each parent, ending at the application root.
  //                  Every _tags/, _include/, _data/ ... lookup walks this list
  // padDir           the directory part of $padPage; padPath is APP plus $padDir
  // padCorrectPath   normalises Windows backslashes
  // padFileName      assembles an output file name from the {file} tag globals - name,
  //                  optional directory, optional date, timestamp and random suffix,
  //                  then the extension
  // padDataFileName  finds a _data/ file by bare name, trying the known extensions in
  //                  turn (xml, json, yaml, csv, php, curl, sql) up the padDirs chain and
  //                  finally in the _common application; returns '' when nothing matches
  // padDataFileData  loads such a file through types/_go/local.php
  // padAddGet        appends one urlencoded key=value, picking ? or & as needed
  // padAddIds        appends the session and request ids, so links keep the request chain
  // padSelfConnect   where a fetch of this site connects: the server's own socket

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
      return substr ( APP, 0, -1 );
    else
      return APP . $padDir;

  }

  function padCorrectPath ( $in ) {

    return str_replace ('\\',  '/', $in );

  }

  function padFileName ( $withDir = TRUE) {

    global $padFileDir, $padFileName, $padFileDate, $padFileTimeStamp, $padFileUniqId, $padFileExtension;

    // The directory joins the name with one /, whichever way it is written: dir='out/' and
    // dir='/out' made out//name and DATA//out/name, which padFilePut refuses for the //.

    if ( $withDir and trim ( (string) $padFileDir, '/' ) !== '' )
      $name = trim ( $padFileDir, '/' ) . "/$padFileName";
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

    global $padCommon;

    // A name stays inside the _data directory it is looked for in: a ../ segment, a
    // backslash or a NUL climbed out of it, and since a data value is sniffed as a file
    // name (padContentType), data=$name with ../../x from the request found any file on
    // the disk - and included and ran it when it was a .php.

    if ( ! is_string ( $check ) or $check === '' or str_contains ( $check, "\0" ) or str_contains ( $check, '\\' )
         or in_array ( '..', explode ( '/', $check ), TRUE ) )
      return '';

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

    // The shared application's _data/ is a fallback like its _tags/ - and gated the same
    // way, so an application that switched $padCommon off reads no data from it either.

    if ( ! $padCommon )
      return '';

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

  // Where the engine's own fetches of this site connect - {get}, {curl 'SELF://...'},
  // {page app=} over HTTP, padPrefetch: $padHost is made of the visitor's Host header, and a
  // request naming Host: attacker.example, or 127.0.0.1:22, had the server fetch from that
  // host and port and splice what came back into the page (and into a {cache} section every
  // later visitor got). The fetch keeps its address - the Host header still picks the site,
  // TLS still checks that name - but connects to the socket this request arrived on,
  // SERVER_ADDR and SERVER_PORT (php -S says its address as SERVER_NAME), as curl's
  // CONNECT_TO. A $padHostBase is an address the configuration gives - a proxy in front, a
  // port the request does not show - and is fetched as it says; so is a request with no
  // server address, the command line. The port is as good as the server makes it: Apache
  // with UseCanonicalName Off (its default) fills SERVER_PORT from a port the Host header
  // names, so such a request still picks the port of this machine the fetch goes to -
  // UseCanonicalPhysicalPort On, or a $padHostBase, pins it.

  function padSelfConnect () {

    global $padHost, $padHostBase;

    if ( ( $padHostBase ?? '' ) !== '' or PHP_SAPI == 'cli' )
      return [];

    $addr = (string) ( $_SERVER ['SERVER_ADDR'] ?? ( PHP_SAPI == 'cli-server' ? ( $_SERVER ['SERVER_NAME'] ?? '' ) : '' ) );
    $port = (string) ( $_SERVER ['SERVER_PORT'] ?? '' );
    $url  = parse_url ( (string) $padHost );

    if ( $addr === '' or ! ctype_digit ( $port ) or ! is_array ( $url ) or ( $url ['host'] ?? '' ) === '' )
      return [];

    $at = $url ['port'] ?? ( strtolower ( $url ['scheme'] ?? '' ) == 'https' ? 443 : 80 );

    if ( str_contains ( $addr, ':' ) and ! str_starts_with ( $addr, '[' ) )
      $addr = "[$addr]";

    return [ $url ['host'] . ":$at:$addr:$port" ];

  }

  // Only when PAD's cookies are off: with them on, the browser and padCurl carry both ids
  // already, and an id in a URL leaks through every Referer header and server log.

  function padAddIds ( $url ) {

    global $padReqID, $padSesID, $padCookies;

    if ( $padCookies )
      return $url;

    $url = padAddGet ( $url, 'padSesID', $padSesID );
    $url = padAddGet ( $url, 'padReqID', $padReqID );

    return $url;

  }

?>
