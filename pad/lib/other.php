<?php


  /**
   * Returns current request information array.
   *
   * @return array Request info with session, request, parent, page, stop, length, start, end, etag.
   */
  function padInfo () {

    return [ 
      'session' => $GLOBALS ['padSesID'] ?? '',
      'request' => $GLOBALS ['padReqID'] ?? '',
      'parent'  => $GLOBALS ['padRefID'] ?? '',
      'page'    => $GLOBALS ['padPage']  ?? '',
      'stop'    => $GLOBALS ['padStop']  ?? '',
      'length'  => $GLOBALS ['padLen']   ?? '',
      'start'   => $_SERVER ['REQUEST_TIME_FLOAT'] ?? 0 , 
      'end'     => microtime (true),
      'etag'    => $GLOBALS ['padEtag']  ?? ''  
    ];

  }


  /**
   * Checks if current request is an include/embedded request.
   *
   * @return bool TRUE if padInclude flag is set.
   */
  function padInclude () {

    if ( isset ( $GLOBALS ['padInclude'] ) and $GLOBALS ['padInclude'] ) 
      return TRUE;
    else
      return FALSE;

  }


  /**
   * Tidies an XML file in place using PHP tidy extension.
   *
   * @param string $file Path to the XML file.
   *
   * @return void
   */
  function padFileXmlTidy ( $file ) {
    
    $options = [
      'input-xml'           => true,
      'output-xml'          => true,
      'force-output'        => true,
      'add-xml-decl'        => false,
      'indent'              => true,
      'tab-size'            => 2,
      'indent-spaces'       => 2,
      'vertical-space'      => 'no',
      'wrap'                => 0,
      'clean'               => 'yes',
      'drop-empty-elements' => 'yes'
    ];

    $data = padFileGet ( $file );

    if ( ! class_exists('tidy') )
      return;

    try {
      $tidy = new tidy;
      $tidy->parseString ( $data, $options, 'utf8' );
      $tidy->cleanRepair();
      $value = $tidy->value ?? '';
    } catch (Throwable $e) {
      return;
    }

    if ( $value and strlen($value) > 10 )
      padFilePut ( $file, $value, 0 );

  }


  /**
   * Checks if this is the second call with given ID.
   *
   * Returns FALSE on first call, TRUE on subsequent calls.
   *
   * @param string $id Unique identifier for the check.
   *
   * @return bool TRUE if called before with same ID.
   */
  function padSecondTime ( $id ) {

    if ( isset ( $GLOBALS ["padSecond$id"] ) )
      return TRUE;

    $GLOBALS ["padSecond$id"] = TRUE;

    return FALSE;

  }


  /**
   * Closes the PHP session safely with error handling.
   *
   * @return void
   */
  function padCloseSession () {

    set_error_handler ( 'padErrorThrow' );

    try {

      padCloseSessionTry ();

    } catch (Throwable $e) {

      // Ignore errors

    }

    restore_error_handler ();

  }


  /**
   * Internal session close logic.
   *
   * Saves session variables and writes session data.
   *
   * @return void
   */
  function padCloseSessionTry () {

    if ( ! isset ( $GLOBALS ['padSessionStarted'] ) or padSecondTime ( 'closeSession' ) )
      return;

    foreach ( $GLOBALS ['padSessionVars'] as $var )
      if ( isset ( $GLOBALS [$var] ) )
        $_SESSION [$var] = $GLOBALS [$var];

    session_write_close ();

  }


  /**
   * Returns constant value if defined, otherwise the input string.
   *
   * @param string $parm Constant name or literal value.
   *
   * @return mixed Constant value or original string.
   */
  function padConstant ( $parm ) {

    if ( defined ( $parm ) )
      return constant ( $parm );
    else
      return $parm;

  }


  /**
   * Processes output string, replacing placeholders.
   *
   * Replaces @pad@ with base URL and @self@ with current page URL.
   *
   * @param string $output The output string to process.
   *
   * @return string Processed output.
   */
  function padOutput ( $output ) {

    global $padGo, $padPage;

    $output = padUnescape ( $output );

    $output = str_replace ( '@pad@',  $padGo,            $output );
    $output = str_replace ( '@self@', $padGo . $padPage, $output );

    return $output;

  }


  /**
   * Finds level index for a tag name.
   *
   * Searches backwards through levels for matching tag.
   *
   * @param string $tag Tag name to find.
   *
   * @return int|false Level index or FALSE if not found.
   */
  function padFindIdx ( $tag ) {

    global $pad, $padTag;

    for ( $i = $pad; $i >= 0 ; $i-- )
      if ( $padTag [$i] == $tag )
        return $i;

    return FALSE;

  }


  /**
   * Executes PAD template code and returns result.
   *
   * @param string $padStrCod PAD template code to execute.
   *
   * @return mixed Execution result.
   */
  function padCode ( $padStrCod ) {

    $GLOBALS ['padStrBox'] = FALSE;
    $GLOBALS ['padStrRes'] = FALSE;
    $GLOBALS ['padStrCln'] = FALSE;
    $GLOBALS ['padStrBld'] = 'code';

    return include PAD . 'start/enter/function.php';

  }


  /**
   * Executes PAD code in sandbox mode with isolated scope.
   *
   * @param string $padStrCod PAD template code to execute.
   *
   * @return mixed Execution result.
   */
  function padSandbox ( $padStrCod ) {

    $GLOBALS ['padStrBox'] = TRUE;
    $GLOBALS ['padStrRes'] = TRUE;
    $GLOBALS ['padStrCln'] = TRUE;
    $GLOBALS ['padStrBld'] = 'code';

    return include PAD . 'start/enter/function.php'; 

  }


  /**
   * Executes PAD code as a function with custom settings.
   *
   * @param string $padStrCod PAD template code.
   * @param bool   $padStrBox Sandbox mode flag.
   * @param bool   $padStrRes Reset flag.
   * @param bool   $padStrCln Clean flag.
   * @param string $padStrFun Function name.
   *
   * @return mixed Execution result.
   */
  function padStrFun (  $padStrCod, $padStrBox, $padStrRes, $padStrCln, $padStrFun ) {

    return include PAD . 'start/function.php';

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
   * Returns current timestamp with microseconds.
   *
   * @return string Timestamp in YmdHisu format.
   */
  function padTimeStamp () {

    $now = DateTime::createFromFormat('U.u', sprintf('%.6f', microtime(TRUE)));
  
    return $now->format('YmdHisu');

  }


  /**
   * Checks if currently inside include, get, page, or example tag.
   *
   * @return bool TRUE if nested inside one of these tags.
   */
  function padInsideOther () {

    global $padTag, $pad;

    for ( $i=$pad; $i; $i--) {
      if ( $padTag [$i] == 'include' ) return TRUE;
      if ( $padTag [$i] == 'get'     ) return TRUE;
      if ( $padTag [$i] == 'page'    ) return TRUE;
      if ( $padTag [$i] == 'example' ) return TRUE;
    }

    return FALSE;

  }


  /**
   * Clears all output buffers and captures content.
   *
   * @param string &$output Receives concatenated buffer contents.
   *
   * @return void
   */
  function padEmptyBuffers ( &$output ) {

    $output = '';

    set_error_handler ( 'padErrorThrow' );

    try {

      $j = ob_get_level (); 
     
      for ( $i = 1; $i <= $j; $i++ ) 
        $output .= ob_get_clean ();

    } catch (Throwable $ignored) {

    }

    restore_error_handler ();

  }


  /**
   * Verifies output buffers are empty, errors if not.
   *
   * @return void
   */
  function padCheckBuffers () {

    padEmptyBuffers ( $output );

    if ( trim ( $output ) )
      return padError ( "Illegal output: '$output'" );

  }


  /**
   * Handles start-and-close tag pattern.
   *
   * @param string $go Walk state to set.
   *
   * @return bool TRUE if pattern matched.
   */
  function padStartAndClose ( $go ) {

    global $pad, $padWalk, $padPrmType;

    if ( $padWalk [$pad] == 'start' and $padPrmType [$pad] == 'close' ) {
      $padWalk [$pad] = $go;
      return TRUE;
    }

    return FALSE;

  }


  /**
   * Checks if value is a simple scalar (not array/object/special).
   *
   * @param mixed $value Value to check.
   *
   * @return bool TRUE if simple scalar value.
   */
  function padSingleValue ( $value ) {

    if ( is_array        ( $value ) or
         is_object       ( $value ) or
         is_resource     ( $value ) or 
         padSpecialValue ( $value ) )

      return FALSE;

    return TRUE;

  }


  /**
   * Checks if value is a special type (TRUE/FALSE/NULL/INF/NAN).
   *
   * @param mixed $value Value to check.
   *
   * @return bool TRUE if special value.
   */
  function padSpecialValue ( $value ) {

    if     ( $value === FALSE ) return TRUE;  
    elseif ( $value === TRUE  ) return TRUE;     
    elseif ( $value === NULL  ) return TRUE;   
    elseif ( $value === INF   ) return TRUE;   
    elseif ( is_float($value) && is_nan($value) ) return TRUE;   
    else                        return FALSE; 

  }


  /**
   * Checks if content name exists in store or app.
   *
   * @param string $content Content name to check.
   *
   * @return bool TRUE if content exists.
   */
  function padContent ( $content ) {

    if ( padStoreCheck      ( $content ) ) return TRUE;
    if ( padAppContentCheck ( $content ) ) return TRUE;

    return FALSE;

  }


  /**
   * Checks if a store name exists in content store.
   *
   * @param string $store Store name to check.
   *
   * @return bool TRUE if store exists.
   */
  function padStoreCheck ( $store ) {

    return isset ( $GLOBALS ['padContentStore'] [$store] );

  }


  /** Checks if page exists in app. @see padAppCheck */
  function padAppPageCheck     ( $check ) { return padAppCheck ( $check              ); }

  /** Checks if include exists in app. @see padAppCheck */
  function padAppIncludeCheck  ( $check ) { return padAppCheck ( "_include/$check"   ); }

  /** Checks if custom tag exists in app. @see padAppCheck */
  function padAppTagCheck      ( $check ) { return padAppCheck ( "_tags/$check"      ); }

  /** Checks if custom function exists in app. @see padAppCheck */
  function padAppFunctionCheck ( $check ) { return padAppCheck ( "_functions/$check" ); }


  /**
   * Checks if a .pad or .php file exists in app directories.
   *
   * @param string $check Path relative to app directory.
   *
   * @return string|false Relative path if found, FALSE otherwise.
   */
  function padAppCheck ( $check ) {

    foreach ( padDirs () as $value )
      if ( padCheck ( APP2 . $value . $check ) )
        return $value . $check ;

    return FALSE;

  }


  /**
   * Checks if .pad or .php file exists at given path.
   *
   * @param string $check Base path without extension.
   *
   * @return bool TRUE if .pad or .php exists.
   */
  function padCheck ( $check ) {

     return  ( file_exists ( "$check.pad" ) or file_exists ( "$check.php" ) ) ;

  }


  /**
   * Checks if script files exist in _scripts directory.
   *
   * @param string $check Script name prefix.
   *
   * @return string|null Glob pattern if found.
   */
  function padScriptCheck ( $check ) {

    foreach ( padDirs () as $value )
      if ( count ( glob ( APP2 . $value . "_scripts/$check*" ) ) )
        return APP2 . $value . "_scripts/$check*";

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
   * Converts associative array to PAD name/value format.
   *
   * @param array $data Input array.
   *
   * @return array Array with name/value structure.
   */
  function padDataForcePad ($data) {

    $result = [];

    foreach ( $data as $name => $value) {
      $result [$name] ['name'] = $name;      
      $result [$name] ['value'] = $value;      
    }

    return $result;
 
  }


  /**
   * Checks if character is valid as first char of identifier.
   *
   * @param string $char Single character.
   *
   * @return bool TRUE if alphabetic.
   */
  function padValidFirstChar ($char) {

    if ( ctype_alpha ( $char) ) return TRUE;
    else                        return FALSE;
 
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


  /**
   * Tidies HTML content using PHP tidy extension.
   *
   * @param string $data     HTML content.
   * @param bool   $fragment If TRUE, output body only.
   *
   * @return string Tidied HTML.
   */
  function padTidy ( $data, $fragment=FALSE ) {

    $config = $GLOBALS ['padTidyConfig'];

    if ( $fragment 
         or isset ( $_REQUEST ['padInclude'] ) 
         or ( isset ( $GLOBALS  ['padInclude'] ) and $GLOBALS ['padInclude'] ) )
      $config ['show-body-only'] = true;

    if ( ! class_exists('tidy') )
      return $data;

    try {
      $tidy = new tidy;
      $tidy->parseString($data, $config, $GLOBALS ['padTidyCcsid'] );
      $tidy->cleanRepair();
      return $tidy->value ?? $data;
    } catch (Throwable $e) {
      return $data;
    }

  }


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
   * Validates that open/close tags are balanced after a point.
   *
   * @param string $string Template content.
   * @param string $check  Point to check from.
   *
   * @return bool TRUE if tags are balanced.
   */
  function padOpenCloseOk ( $string, $check) {

    if ( strpos ( $string, $check ) === FALSE )
      return FALSE;

    list ( $dummy, $string ) = explode ( $check, '.' . $string . '.', 2 );

    $tags = padOpenCloseList ( $string );

    return padOpenCloseCount ( $string, $tags);

  }


  /**
   * Extracts list of closing tags from template string.
   *
   * @param string $string Template content.
   *
   * @return array Tag names found as closing tags.
   */
  function padOpenCloseList ( $string ) {

    $tags = [];
    
    $p1 = strpos($string, '{/', 0);

    while ($p1 !== FALSE) {

      $p2 = strpos($string, '}', $p1);

      if ( $p2 !== FALSE ) {

        $p3 = strpos($string, ' ', $p1);
        if ($p3 !== FALSE and $p3 < $p2 )
          $p2 = $p3;      

        $tag = substr($string, $p1+2, $p2-$p1-2);
        if ( padValidTag ($tag) )
          $tags [$tag] = TRUE;

      }

      $p1 = strpos($string, '{/', $p1+1);

    }

    return $tags;

  }


  /**
   * Verifies all tags in list are balanced.
   *
   * @param string $string Template content.
   * @param array  $tags   Tag names to check.
   *
   * @return bool TRUE if all balanced.
   */
  function padOpenCloseCount ( $string, $tags ) {

   foreach ( $tags as $tag => $dummy )
      if ( ! padOpenCloseCountOne ( $string, $tag ) )
        return FALSE;

    return TRUE;  

  }


  /**
   * Checks if single tag is balanced (opens equal closes).
   *
   * @param string $string Template content.
   * @param string $tag    Tag name to check.
   *
   * @return bool TRUE if balanced.
   */
  function padOpenCloseCountOne ( $string, $tag ) {

    if ( ( substr_count($string, '{'.$tag.' ' ) + substr_count($string, '{'.$tag.'}' ) )
           <> 
         ( substr_count($string, '{/'.$tag.' ') + substr_count($string, '{/'.$tag.'}') ) )
      return FALSE;

    return TRUE;  

  }


  /**
   * Checks if tag opens and closes are balanced.
   *
   * @param string $tag    Tag name.
   * @param string $string Template content.
   *
   * @return bool TRUE if balanced.
   */
  function padCheckTag ($tag, $string) {

    return ( substr_count($string, "{".$tag.' ') == substr_count($string, "{/" . $tag.'}') ) ;

  }


  /**
   * Splits string into before and after parts.
   *
   * @param string $needle   Delimiter.
   * @param string $haystack String to split.
   * @param string &$before  Receives part before delimiter.
   * @param string &$after   Receives part after delimiter.
   *
   * @return void
   */
  function padSplit ( $needle, $haystack, &$before, &$after ) {

    $array = explode ( $needle, $haystack, 2 );

    $before = trim ( $array [0] ?? '' );
    $after  = trim ( $array [1] ?? '' );

  }


  /**
   * Returns current request ID or generates unique ID.
   *
   * @return string Request ID.
   */
  function padID () {

    return $GLOBALS ['padReqID'] ?? uniqid (TRUE);

  }


  /**
   * Logs error message to PHP error log.
   *
   * @param string $error Error message.
   *
   * @return void
   */
  function padLogError ( $error ) {

    error_log ( '[PAD] ' . padID () . ' ' . padMakeSafe ( $error ), 4 );

  }


  /**
   * Sanitizes input for safe logging/display.
   *
   * Removes control characters and truncates to length.
   *
   * @param mixed $input Input value.
   * @param int   $len   Maximum length.
   *
   * @return string Sanitized string.
   */
  function padMakeSafe ( $input, $len=2048 ) {

    if ( is_array($input) or is_object($input) ) 
      $input = padJson ($input);

    $input = preg_replace('/[\x00-\x1F\x7F-\xFF]/', ' ', $input);
    $input = preg_replace('/\s+/', ' ', $input);
    
    if ( strlen($input) > $len )
      $input = substr ( $input, 0, $len );
    
    $input = trim($input);

    return $input;

  }


  /**
   * Splits string by delimiter with entity restoration.
   *
   * Restores escaped entities (&pipe;, &eq;, &comma;) after split.
   *
   * @param string $haystack String to split.
   * @param string $limit    Delimiter character.
   * @param int    $number   Max parts (0 = unlimited).
   *
   * @return array Array of trimmed parts.
   */
  function padExplode ( $haystack, $limit, $number=0 ) {

    if ($number)
      $explode = explode ( $limit, $haystack, $number );
    else
      $explode = explode ( $limit, $haystack );
    
    foreach ($explode as $key => $value ) {

      $explode [$key] = trim($value);
    
      if ( $limit == '|' ) $explode [$key] = str_replace ( '&pipe;',  '|', $explode [$key] );
      if ( $limit == '=' ) $explode [$key] = str_replace ( '&eq;',    '=', $explode [$key] );
      if ( $limit == ',' ) $explode [$key] = str_replace ( '&comma;', ',', $explode [$key] );
    
      if ( $explode [$key] === '' )
        unset ( $explode [$key] );

    }

    return array_values ( $explode );
    
  }


  /**
   * Encodes data as JSON with pretty printing.
   *
   * @param mixed $data Data to encode.
   *
   * @return string JSON string or '{}' on error.
   */
  function padJson ( $data ) {

    set_error_handler ( 'padErrorThrow' );

    try {

      $return = json_encode ( $data, JSON_PRETTY_PRINT | JSON_PARTIAL_OUTPUT_ON_ERROR | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_NUMERIC_CHECK );
    
    } catch (Throwable $e) {

      $return = '{}';

    }

    restore_error_handler ();    

    return $return;

  }


  /**
   * Safely converts value to array.
   *
   * @param mixed $xxx Value to convert.
   *
   * @return array Converted array or empty array on failure.
   */
  function padToArray ($xxx) {

    if ( is_array($xxx) )
      return ($xxx);

    set_error_handler ( function ($s, $m, $f, $l) { return; } );
    $error_level = error_reporting(0);

    $array = [];

    try {
      $array = (array) $xxx;
    }
    catch (Throwable $e) {
      $array = [];
    }
    
    if     ( $array === NULL     )  $array = [];
    elseif ( ! is_array ($array) )  $array = [];

    error_reporting($error_level);
    restore_error_handler();

    return $array;
    
  }


  /**
   * Sends HTTP header if not already sent.
   *
   * @param string $header Header string.
   *
   * @return void
   */
  function padHeader ($header) {

    if ( headers_sent () )
      return;

    header ($header);

    $GLOBALS ['padHeaders'] [] = $header;
 
  }


  /**
   * Strips leading $ from field name.
   *
   * @param string $parm Field name possibly with $.
   *
   * @return string Field name without $.
   */
  function padFieldName ($parm) {
    
    return (substr($parm, 0, 1) == '$') ? substr($parm, 1) : $parm;

  }


  /** Returns URL-safe base64 encoded MD5 hash (22 chars). */
  function padMD5 ($input) {
    return substr(padBase64(padPack(md5($input))),0,22);
  }

  /** Unpacks URL-safe base64 MD5 back to hex. */
  function padMD5Unpack ($input) {
    return padUnpack(padUnbase64 ($input.'=='));
  }

  /** Packs hex string to binary. */
  function padPack ($data) {
    return pack('H*',$data);
  }

  /** Unpacks binary to hex string. */
  function padUnpack ($data) {
    return unpack('H*',$data)[1];
  }

  /** URL-safe base64 encode (replaces +/ with _-). */
  function padBase64 ($string) {
    return strtr(base64_encode($string),'+/','_-');
  }

  /** URL-safe base64 decode (replaces _- with +/). */
  function padUnbase64 ($string) {
    return base64_decode(strtr($string,'_-','+/'));
  }

  /**
   * Generates cryptographically secure random string.
   *
   * @param int $len Length of string.
   *
   * @return string Random alphanumeric string.
   */
  function padRandomString ($len=8) {
    $random = ceil(($len/4)*3);
    $random = random_bytes($random);
    $random = base64_encode($random);
    $random = substr($random,0,$len);
    $random = str_replace ( '+', padRandomChar(), $random );
    $random = str_replace ( '/', padRandomChar(), $random );
    return $random;
  }

  /** Returns random alphanumeric character. */
  function padRandomChar () {
    $random = mt_rand(0,61);
    return ($random < 10) ? chr($random+48) : ($random < 36 ? chr($random+55) : chr($random+61));
  }


  /**
   * Restores escaped PAD entities to original characters.
   *
   * @param string $string String with entities.
   *
   * @return string String with restored characters.
   */
  function padUnescape ( $string ) {

    return str_replace ( [ '&open;','&close;','&pipe;', '&eq;','&comma;','&at;', '&else;' ], 
                         [ '{',     '}',      '|',      '=',   ',',      '@',    '{else}' ], 
                         $string );
  }


  /**
   * Escapes special PAD characters to entities.
   *
   * @param string $string String to escape.
   *
   * @return string String with escaped entities.
   */
  function padEscape ( $string ) {

    return str_replace ( [ '{',     '}',      '|',      '=',    ',',     '@'    ], 
                         [ '&open;','&close;','&pipe;', '&eq;','&comma;','&at;' ], 
                         $string );
  }


  /**
   * Compresses data using gzip.
   *
   * @param string $data Data to compress.
   *
   * @return string Compressed data.
   */
  function padZip ($data) {

    return gzencode($data);

  }


  /**
   * Decompresses gzip data.
   *
   * @param string $data Compressed data.
   *
   * @return string Decompressed data.
   */
  function padUnzip ($data) {

    return gzdecode($data);

  }


  /**
   * Calculates duration in nanoseconds.
   *
   * @param float $start Start time (default: request start).
   * @param float $end   End time (default: now).
   *
   * @return int Duration in nanoseconds.
   */
  function padDuration ( $start = 0, $end = 0 ) {

    if ( ! $start ) $start = $_SERVER ['REQUEST_TIME_FLOAT'] ?? $GLOBALS ['padMicro'] ?? microtime ( true );
    if ( ! $end   ) $end   = microtime ( true );

    $duration = (int) ( ( $end - $start ) * 1000000000 );

    return $duration;

  }


  /**
   * Calculates high-resolution duration in nanoseconds.
   *
   * @param int $start Start hrtime (default: request start).
   * @param int $end   End hrtime (default: now).
   *
   * @return int Duration in nanoseconds.
   */
  function padDurationHR ( $start = 0, $end = 0 ) {

    if ( ! $start ) $start = $GLOBALS ['padHR'] ?? hrtime ( TRUE );
    if ( ! $end   ) $end   = hrtime ( TRUE );

    return $end - $start;

  }


  /**
   * Extracts text between delimiters.
   *
   * @param string $string  Input string.
   * @param string $open    Opening delimiter.
   * @param string $close   Closing delimiter.
   * @param string &$before Receives text before open.
   * @param string &$between Receives text between delimiters.
   * @param string &$after  Receives text after close.
   *
   * @return bool TRUE if delimiters found.
   */
  function padBetween ( $string, $open, $close, &$before, &$between, &$after ) {

    $before = $between = $after = '';

    $p1 = strpos ( $string, $open );
    if ( $p1 === FALSE ) return FALSE;

    $start = $p1 + strlen($open);
    $p2 = strpos ( $string, $close, $start );
    if ( $p2 === FALSE ) return FALSE;

    if ( $p1 > 0 )
      $before = substr ( $string, 0, $p1 );

    $between = substr ( $string, $start, $p2 - $start );

    $afterPos = $p2 + strlen($close);
    if ( $afterPos < strlen ( $string ) )
      $after = substr ( $string, $afterPos );

    return TRUE;

  }


  /**
   * Creates numeric range from string notation.
   *
   * @param string $input     Range string (e.g., "1..10").
   * @param int    $increment Step value.
   *
   * @return array Numeric range array.
   */
  function padGetRange ( $input, $increment=1 ) {

    $parts = padExplode ($input, '..');

    $p1 = $parts[0] ?? '';
    $p2 = $parts[1] ?? '';

    if ( $p2 )
      return range ( $p1, $p2, $increment );
    elseif ( $p1 )
      return range ( 1, $p1, $increment );
    else
      return range ( 1, 10, $increment );

  }


  /**
   * Parses semicolon-separated list, converting numeric strings.
   *
   * @param string $list Semicolon-separated values.
   *
   * @return array Parsed list.
   */
  function padGetList ( $list ) {

    $list = explode ( ';', $list );

    foreach ( $list as $key => $value)
      if ( is_numeric ($value) )
        $list [$key] = intval($value);

    return $list;

  }


  /**
   * Executes a function using the eval/type system.
   *
   * @param string $name   Function name.
   * @param string $myself Caller identifier.
   * @param array  $parm   Function parameters.
   *
   * @return mixed Function result.
   */
  function padFunctionAsTag ( $name, $myself, $parm ) {

    $k = 100;

    $result [$k] [0] = $name;
    $result [$k] [1] = 'TYPE';
    $result [$k] [2] = padTypeFunction ( $name );        
    $result [$k] [3] = 0;       

    foreach ( $parm as $key => $val ) 
      if ( $key > 0 ) {
        $k = $k + 100;
        $result [$k] [0] = $val;;
        $result [$k] [1] = 'VAL';
      }

    padEvalType ( $result, $myself );

    $start = array_key_first ( $result );

    return $result [$start] [0];

  }


  /**
   * Executes a tag as if it were a function call.
   *
   * @param string $tag   Tag name.
   * @param string $value Content between tags.
   * @param array  $parms Tag parameters.
   *
   * @return mixed Tag result.
   */
  function padTagAsFunction ( $tag, $value, $parms ) {
  
    $extra = '';

    foreach ( $parms as $parm )
      $extra .= " '" . str_replace( "'", "\\'", $parm) . "',";

    if ( $extra )
      $extra = substr ( $extra, 0, -1 );

    return padCode ( '{' . $tag . $extra . '}' . $value . '{/' . $tag . '}' );

  }


  /**
   * Converts value to boolean flag.
   *
   * @param mixed $input Value to convert.
   *
   * @return bool Boolean result.
   */
  function padMakeFlag ( $input ) {

    if     ( $input === NULL  )          return FALSE;
    elseif ( $input === FALSE )          return FALSE;
    elseif ( $input === TRUE  )          return TRUE;
    elseif ( strlen(trim($input)) == 0 ) return FALSE;

    if ( is_array ($input) or is_object ($input) or is_resource ($input) )  {

      $array = padToArray( $input );

      if ( padIsDefaultData ( $array )  )
        return FALSE;

      if ( count ( $array ) )
        return TRUE; 
      else
        return FALSE;

    }
 
    if ( padEval($input) )
      return TRUE; 
    else
      return FALSE;

  }


  /**
   * Converts value to content string.
   *
   * @param mixed $input Value to convert.
   *
   * @return string Content string.
   */
  function padMakeContent ( $input ) {    

    if     ( $input === NULL        )  return '';
    elseif ( $input === FALSE       )  return '';
    elseif ( $input === TRUE        )  return '1';
    else                               return $input; 

  }


  /**
   * Sets global variable with level-scoped save/restore.
   *
   * Saves previous value to restore when level exits.
   *
   * @param string $name  Variable name.
   * @param mixed  $value Value to set.
   *
   * @return void
   */
  function padSetGlobalLvl ( $name, $value ) {

    if ( ! padValidVar($name) ) 
      return;

    if ( $value === NULL )
      $value = '';

    global $pad, $padSaveLvl, $padDeleteLvl;
    
    if ( array_key_exists($name, $GLOBALS) and ! array_key_exists ($name, $padSaveLvl [$pad]) )
      $padSaveLvl [$pad] [$name] = $GLOBALS [$name];

    if ( ! array_key_exists ($name,  $GLOBALS) )
      $padDeleteLvl [$pad] [] = $name;
    else
      unset ( $GLOBALS [$name] );

    $GLOBALS [$name] = $value;

  }


  /**
   * Sets global variable with occurrence-scoped save/restore.
   *
   * Saves previous value to restore when occurrence ends.
   *
   * @param string $name  Variable name.
   * @param mixed  $value Value to set.
   *
   * @return void
   */
  function padSetGlobalOcc ( $name, $value ) {

    if ( ! padValidVar($name) ) 
      return;

    if ( $value === NULL )
      $value = '';

    global $pad, $padSaveOcc, $padDeleteOcc;
    
    if ( array_key_exists($name, $GLOBALS) and ! array_key_exists ($name, $padSaveOcc [$pad]) )
      $padSaveOcc [$pad] [$name] = $GLOBALS [$name];

    if ( ! array_key_exists ($name,  $GLOBALS) )
      $padDeleteOcc [$pad] [] = $name;
    else
      unset ( $GLOBALS [$name] );

    $GLOBALS [$name] = $value;

  }


  /**
   * Restores globals saved at level start.
   *
   * @return void
   */
  function padResetLvl () {

    global $pad, $padSaveLvl, $padDeleteLvl;

    foreach ( $padSaveLvl [$pad] as $key => $value) {
      if ( isset ( $GLOBALS [$key] ) ) 
        unset ($GLOBALS [$key] );
      $GLOBALS [$key] = $value;
    }

    foreach ( $padDeleteLvl [$pad] as $key)
      if ( isset ( $GLOBALS [$key] ) )
        unset ( $GLOBALS [$key] );

  }


  /**
   * Restores globals saved at occurrence start.
   *
   * @return void
   */
  function padResetOcc () {

    global $pad, $padSaveOcc, $padDeleteOcc;

    foreach ( $padSaveOcc [$pad] as $key => $value) {
      if ( isset ( $GLOBALS [$key] ) ) 
        unset ($GLOBALS [$key] );
      $GLOBALS [$key] = $value;
    }

    foreach ( $padDeleteOcc [$pad] as $key)
      if ( isset ( $GLOBALS [$key] ) )
        unset ( $GLOBALS [$key] );

  }


  /**
   * Returns default empty data structure.
   *
   * @return array Default data array.
   */
  function padDefaultData () {
    
    return [ 999 => [] ];

  }


  /**
   * Checks if data matches default empty structure.
   *
   * @param mixed $data Data to check.
   *
   * @return bool TRUE if default data.
   */
  function padIsDefaultData ( $data ) {
    
    if ( ! is_array ( $data ) ) return FALSE;
    if ( count ( $data ) <> 1 ) return FALSE;

    $key = array_key_first ( $data );

    if ( ! is_array ( $data [$key] ) ) return FALSE;
    if ( count ( $data [$key] )      ) return FALSE;
    
    return TRUE;

  }


  /**
   * Checks if tag has array data at any level.
   *
   * @param string $tag Tag name to check.
   *
   * @return bool TRUE if array exists.
   */
  function padChkLevel ($tag) {

    global $padCurrent, $pad;

    for ( $search = $pad; $search>=0; $search-- )
      if ( isset ( $padCurrent [$search] [$tag] ) and is_array ( $padCurrent [$search] [$tag]) )
        return TRUE;

    return FALSE;

  }


  /**
   * Gets array data for tag from any level.
   *
   * @param string $tag Tag name to find.
   *
   * @return array|null Array data or null.
   */
  function padGetLevelArray ($tag) {

    global $padCurrent, $pad;

    for ( $search = $pad; $search>=0; $search-- )
      if ( isset ( $padCurrent [$search] [$tag] ) and is_array ( $padCurrent [$search] [$tag]) )
        return $padCurrent [$search] [$tag];

  }


  /**
   * Gets tag parameter value with default.
   *
   * @param string $parm    Parameter name.
   * @param mixed  $default Default value.
   *
   * @return mixed Parameter value or default.
   */
  function padTagParm ($parm, $default='') {

    global $pad, $padPrm;

    padDone ($parm);

    if ( isset ( $padPrm [$pad] [$parm] ) )
      return $padPrm [$pad] [$parm];
    else
      return $default;

  }


  /**
   * Marks a parameter/option as processed.
   *
   * @param string $var Parameter name.
   * @param mixed  $val Value to store.
   *
   * @return void
   */
  function padDone ( $var, $val=TRUE ) {
    
    $GLOBALS ['padDone'] [$GLOBALS ['pad']] [$var] = $val;

  }

  /**
   * Checks if parameter was already processed.
   *
   * @param string $var Parameter name.
   *
   * @return bool TRUE if processed.
   */
  function padIsDone ( $var ) {
    
    return isset ( $GLOBALS ['padDone'] [$GLOBALS ['pad']] [$var] );

  }


  /**
   * Detects content type from content string.
   *
   * Returns: list, json, yaml, xml, pad, html, range, curl, file, or csv.
   *
   * @param string &$content Content string (may be modified).
   *
   * @return string Content type identifier.
   */
  function padContentType ( &$content ) {

    $content = trim ( $content );

    if ( substr($content, 0, 1) == '(' and substr($content, -1) == ')' )
      $type = 'list';
    elseif ( substr ($content, 0, 6) == '&open;') 
      $type = 'json';
    elseif ( substr ($content, 0, 5) == '%YAML' )
      $type = 'yaml';
    elseif ( substr ($content, 0, 3) == '---' )
      $type = 'yaml';
    elseif ( substr ( $content, 0, 5) == '<?xml')
      $type = 'xml';
    elseif ( strpos ( $content, '<!DOCTYPE') !== FALSE ) {
      $open   = strpos  ($content, '<!DOCTYPE');
      $close  = strpos  ($content, '>', $open);
      $check  = stripos ($content, 'pad', $open);
      if ($check !== FALSE and $check < $close )
        $type = 'pad';
      else
        $type = 'xml';
    }
    elseif ( substr ($content, 9, 5) == '<html' )
      $type = 'html';
    elseif ( substr($content, 0, 1) == '<')
      $type = 'xml';
    elseif ( substr($content, 0, 1) == '{')
      $type = 'json';
    elseif ( substr($content, 0, 1) == '[')
      $type = 'json';
    elseif ( substr($content, 0, 1) == '(')
      $type = 'json';
    elseif ( substr($content, -1) == ')')
      $type = 'json';
    else
      $type = '';
      
    if ( $type )
      return $type;

    $first = strpos ($content, '({');
    $last  = strpos ($content, '})');
    if ($first !== FALSE and $last !== FALSE and $first < $last ) {
      $type = 'json';
      $content = substr($content, $first+1, $last-$first);
      return $type;
    }

    $first = strpos ($content, '([');
    $last  = strpos ($content, '])');
    if ($first !== FALSE and $last !== FALSE and $first < $last ) {
      $type = 'json';
      $content = substr($content, $first+1, $last-$first);
      return $type;
    }

    $parts = padExplode ($content, '..');
    if ( count ($parts) == 2 and ctype_alnum($parts[0]) and ctype_alnum($parts[1]) )
      return 'range';

    if ( str_starts_with ( strtolower ( $content ), 'http:' ) 
      or str_starts_with ( strtolower ( $content ), 'https:' )  )
      return 'curl';

    if ( padDataFileName ( $content ) )
      return 'file';

    return 'csv';

  }


  /**
   * Checks if field name is valid for app storage.
   *
   * Rejects pad*, pq*, and PHP superglobals.
   *
   * @param string $fld Field name.
   *
   * @return bool TRUE if valid for storage.
   */
  function padValidStore ($fld) {

    if ( substr($fld, 0,3) == 'pad' )
      return FALSE;

    if ( substr($fld, 0,2) == 'pq' )
      return FALSE;

    if ( in_array ( $fld, ['GLOBALS','_POST','_GET','_COOKIE','_SESSION','_FILES','_SERVER','_REQUEST','_ENV'] ) )
      return FALSE;

    return TRUE;

  }


  /**
   * Checks if field is internal PAD variable (non-storable).
   *
   * @param string $field Field name.
   *
   * @return bool TRUE if internal PAD variable.
   */
  function padStrPad ( $field ) {

    if ( str_starts_with ( $field, 'pad' ) or str_starts_with ( $field, 'pq' ) ) 
      if ( ! str_starts_with ( $field, 'padStr' ) )
        if ( ! in_array ( $field, padStrSto) )
          if ( ! in_array ( $field, padLevelVars) )
            if ( $field <> 'padInfoCnt' and $field <> 'padInfoTraceId' )
              return TRUE;
         
    return FALSE;

  }


  /**
   * Filters array to keep only items in range.
   *
   * @param array &$vars  Array to filter (modified in place).
   * @param int   $start  First item to keep.
   * @param int   $end    Last item to keep.
   *
   * @return void
   */
  function padDataFilterGo (&$vars, $start, $end) {

    $now = 0;
    foreach ( $vars as $key => $value ) {
      $now++;
      if ($now < $start or $now > $end)
        unset($vars [$key]);
    }

  }


  /**
   * Filters array with range and optional count limit.
   *
   * @param array &$vars  Array to filter (modified in place).
   * @param int   $start  First item to keep.
   * @param int   $end    Last item to keep.
   * @param int   $count  Max items (0 = unlimited).
   *
   * @return void
   */
  function padHandGo ( &$vars, $start, $end, $count=0 ) {

    global $hit, $now;

    $now = $hit = 0;

    foreach ( $vars as $key => $value ) {
    
      $now++;
    
      if ( $now < $start or $now > $end ) 
        unset ( $vars [$key] );
      else
        $hit++;

      if ( $count and $hit > $count )
        unset ( $vars [$key] );

    }

  }

  
?>