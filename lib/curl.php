<?php

  function padInclude ( $app, $page='index', $query='' ) {

    return pad ( $app, $page, $query, 1 );
    
  }

  function pad ( $app, $page='index', $query='', $include='' ) {

    $result = padComplete ( $app, $page, $query, $include );

    return $result ['data'];
    
  }

  function padComplete ( $app, $page='index', $query='', $include='' ) {

    global $padHost, $padScript;

    if ($include)
      $include = '&padInclude=1';

    $input = $output = [];

    $input ['url'] = "$padHost$padScript?app=$app&page=$page$query$include";

    $input ['cookies'] ['PADSESSID'] = $GLOBALS ['PADSESSID'];
    $input ['cookies'] ['PADREQID']  = $GLOBALS ['PADREQID'];
    
    return padCurl ($input);
    
  }

  function padCurlData ($input) {    

    $curl = padCurl ($input);
    
    return $curl ['data'];

  }

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

    if ( ! is_array($input) )
      $input = [ 'url' => $input ];

    $output             = [];
    $output ['input']   = $input;
    $output ['options'] = [];
    $output ['result']  = '999';  //  200 / 404 / etc
    $output ['type']    = '';     //  'xml' , 'html' , 'json' , 'yaml' , 'csv' , ''
    $output ['info']    = [];
    $output ['headers'] = [];
    $output ['cookies'] = [];
    $output ['data']    = '';

    if ( isset($input['get']) ) {
      $str = ( strpos ($input ['url'], '?' ) === FALSE ) ? '?' : '&';
      foreach ( $input['get'] as $key => $val ) {
        $input ['url'] .= $str . $key . '=' . urlencode($val);
        $str = '&';
      }
    }

    $output ['url'] = $input ['url'] ;  

    if ( ! strpos( $input ['url'], '://') ) {
      $file = APP . 'data/' . $input ['url'];
      if ( file_exists ( $file ) ) {
        $output ['data']    = padFileGetContents ( $file );   
        $output ['type']    = padContentType  ( $output ['data'] );   
        $output ['result']  = '200';
      } else {
        $output ['data']    = '';      
        $output ['result']  = '404';
      }
      $GLOBALS ['padCurlLast'] = $output;
      return $output;
    }

    $options = $input ['options'] ?? [];
 
    padCurlOpt ($options, 'RETURNTRANSFER', true);
    padCurlOpt ($options, 'ENCODING',       'gzip' );
    padCurlOpt ($options, 'FOLLOWLOCATION', true);
    padCurlOpt ($options, 'HEADER',         true);
    padCurlOpt ($options, 'USERAGENT',      $_SERVER['HTTP_USER_AGENT'] ?? 'Mozilla/5.0 (X11; CrOS x86_64 13904.77.0) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.147 Safari/537.36 PAD/10.0');
    padCurlOpt ($options, 'REFERER',        $GLOBALS ['padPageExternal'] . $GLOBALS ['page']);

    if ( isset($input['user']) )
      padCurlOpt ($options, 'USERPWD', $input['user'] . ":" . $input['$padassword']);
    
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

    $curl = curl_init ( $input ['url'] );

    if ($curl === FALSE)
      return padCurlError ($output, 'curl_init = FALSE');

    foreach ( $options as $key => $val )
      curl_setopt ( $curl, constant('CURLOPT_'.$key), $val );
  
    padTimingStart ('curl');
    $result          = curl_exec    ($curl);
    $output ['info'] = curl_getinfo ($curl);
    padTimingEnd   ('curl');

    if ($result  === FALSE)
      return padCurlError ($output, 'curl_exec = FALSE');
    
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
            $file = padBetween ($value, '"', '"');
        
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
 
    if ($GLOBALS ['padTrace'])
      padCurlTrace ( $output );

    $GLOBALS ['padCurlLast'] = $output;

    return $output;
    
  }

  function padCurlTrace ( $trace ) {

    global $pad;
    
    $file = $GLOBALS ['padLevelDir'] [$GLOBALS ['pad']]. "/curl_" . padRandomString(). ".json";

    padFilePutContents ($file, padJson ($trace) );

  }
  
  function padCurlOpt (&$options, $name, $value) {
    
    if ( ! isset ( $options [$name] ) )
      $options [$name] = $value;

  }

  function padCurlError ($output, $error) {

    $output ['ERROR'] = $error;

     if ($GLOBALS ['padTrace'])
      padCurlTrace ( $output );

    $GLOBALS ['padCurl_last'] = $output;

    return $output;

  }

?>