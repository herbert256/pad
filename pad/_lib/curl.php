<?php


  function padCurl ($input) {

    //  Required input parms
    //  - ['url']
    
    //  Optional input parms
    //  - ['user']
    //  - ['password']
    //  - ['get']
    //  - ['post']
    //  - ['cookies']
    //  - ['headers']
    //  - ['options']

    $output             = [];
    $output ['url']     = '';
    $output ['input']   = $input;
    $output ['options'] = [];
    $output ['result']  = '999';  //  200 / 404 / etc
    $output ['type']    = '';     //  'xml' , 'pad' , 'json' , 'yaml' , 'csv' , ''
    $output ['info']    = [];
    $output ['headers'] = [];
    $output ['cookies'] = [];
    $output ['data']    = '';

    $url = ( is_array($input) ) ? $input ['url'] : $input;

    if ( ! is_array($input) ) 
      $input = [];

    if ( isset($input['get']) )
      foreach ( $input['get'] as $key => $val ) 
        $url = padAddGet ( $url, $key, $val );

    $output ['url'] = $url;

    if ( ! strpos( $url, '://') ) {

      $check = padDataFileName ( $url );
    
      if ( $check ) {

        $output ['url']    = "file://$check";
        $output ['data']   = padDataFileData ( $check );   
        $output ['type']   = padContentType  ( $output ['data'] );   
        $output ['result'] = '200';

        $GLOBALS ['padCurlLast'] = $output;

        return $output;

      } 

    }

    if ( ! strpos( $url, '://') ) {
      $url = $GLOBALS ['padHost'] . $GLOBALS ['padGo']  . $url;
      $output ['url'] = $url;
    }

    if ( str_starts_with ( strtolower ( $url ), strtolower ( $GLOBALS['padHost'] ) ) ) {
      $input ['cookies'] ['padSesID'] = $GLOBALS ['padSesID'];
      $input ['cookies'] ['padReqID'] = $GLOBALS ['padReqID'];
    }

    $options = $input ['options'] ?? [];
 
    padCurlOpt ($options, 'RETURNTRANSFER', true);
    padCurlOpt ($options, 'ENCODING',       'gzip' );
    padCurlOpt ($options, 'FOLLOWLOCATION', true);
    padCurlOpt ($options, 'HEADER',         true);
    padCurlOpt ($options, 'USERAGENT',      $_SERVER['HTTP_USER_AGENT'] ?? 'Mozilla/5.0 (X11; CrOS x86_64 13904.77.0) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.147 Safari/537.36 pad/10.0');
    padCurlOpt ($options, 'REFERER',        $GLOBALS ['padGoExt'] . $GLOBALS ['padPage']);

    if ( isset($input['user']) )
      padCurlOpt ($options, 'USERPWD', $input['user'] . ":" . $input['password']);
    
    if ( isset($input['post']) ) {
      padCurlOpt ($options, 'POST', true);
      padCurlOpt ($options, 'POSTFIELDS', $input ['post']);
    }
 
    if ( isset($input['cookies']) ) {
      $cookies = '';
      foreach ( $input['cookies'] as $key => $val ) {
        if ($cookies)
          $cookies .= '; ';
        $cookies .= $key . '=' . $val;
      }
      padCurlOpt ($options, 'COOKIE', $cookies);
    }
  
    if ( isset($input['headers']) ) {
      $headers_in = [];
      foreach ( $input['headers'] as $key => $val )
        $headers_in [] = $key . ': ' . $val;
      padCurlOpt ($options, 'HTTPHEADER', $headers_in);
    }

    $output ['options'] = $options;      

    set_error_handler ( 'padThrow' );
    $errorReporting = error_reporting (0);

    try {

      $curl = curl_init ( $url );

      if ($curl === FALSE)
        return padCurlError ($output, 'curl_init = FALSE');

      foreach ( $options as $key => $val )
        curl_setopt ( $curl, constant('CURLOPT_'.$key), $val );
    
      $result          = curl_exec    ($curl);
      $output ['info'] = curl_getinfo ($curl);
      
      if ($result === FALSE)
        return padCurlError ($output, 'curl_exec = FALSE');

    } catch (Throwable $e) {

      return padCurlError ( $output,  $e->getFile() . ':' . $e->getLine() . ' ' . $e->getMessage() );

    }

    restore_error_handler ();
    error_reporting ( $errorReporting );

    if ( isset ( $output ['info'] ['http_code'] ) )
      $output ['result'] = $output ['info'] ['http_code'];
    else
      $output ['result'] = 'xxx';

    if ( isset($output ['info']['header_size']) and $output ['info']['header_size'] > 0 ) {
        
      $file = '';
      $headers = explode( "\r\n", substr ( $result, 0, $output ['info'] ['header_size'] ) );

      foreach ($headers as $key => $val) {
        
        $work = explode ( ':', $val, 2 );
        
        $header = trim ( $work [0] ?? '' );
        $value  = trim ( $work [1] ?? '' );

        if ( $header and ! $value )
      
          $output ['headers'] ['http'] = $header;
        
        elseif ( $header and $value ) {

          if ( $header == 'Content-Disposition' and !$file)
            padBetween ($value, '"', '"', $before, $file, $after);
        
          if ( $header == 'Content-Type' )
            if     (strpos ($value, 'html')       !== FALSE) $output ['type'] = 'html';
            elseif (strpos ($value, 'xml')        !== FALSE) $output ['type'] = 'xml';
            elseif (strpos ($value, 'json')       !== FALSE) $output ['type'] = 'json';
            elseif (strpos ($value, 'javascript') !== FALSE) $output ['type'] = 'json';
            elseif (strpos ($value, 'csv')        !== FALSE) $output ['type'] = 'csv';
            elseif (strpos ($value, 'yaml')       !== FALSE) $output ['type'] = 'yaml';
            elseif (strpos ($value, 'yml')        !== FALSE) $output ['type'] = 'yaml';
         
          if ( $header == 'Set-Cookie') {
            $first = strpos ($value, '=');
            $last  = strpos ($value, ';');
            if ( $first !== FALSE and $last !== FALSE and $first > 0 and $last > $first )
             $output ['cookies'] [substr($value, 0, $first)] = substr($value, $first+1, $last-$first-1);
          }
          else
            $output ['headers'] [$header] = $value;
        
        }

      }
      
    }

    if ( isset($output ['info']['header_size']) )
      $output ['data'] = trim(substr($result, $output ['info']['header_size']));
    else
      $output ['data'] = trim($result);

    if ( ! $output ['type'] and $file) {
      $pados = strrpos($file, '.');
      if ( ! $pados !==FALSE)
        $output ['type'] = substr($parm, $pados+1);
    }

    if ( ! $output ['type'] )
      $output ['type'] = padContentType ( $output ['data'] );

    if ( padTrace )
      include pad . 'info/events/curl.php';

    $GLOBALS ['padCurlLast'] = $output;

    return $output;
    
  }
  
  function padCurlOpt (&$options, $name, $value) {
    
    if ( ! isset ( $options [$name] ) )
      $options [$name] = $value;

  }

  function padCurlError ($output, $error) {

    $output ['ERROR']  = $error;
    $output ['result'] = '999';

    $GLOBALS ['padCurlLast'] = $output;

    return $output;

  }

?>