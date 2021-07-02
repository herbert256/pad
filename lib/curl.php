<?php

  function pad_include ($url) {

    global $pad_host, $pad_script, $app;

    $input  = [];
    $output = [];

    $input ['url'] = "$pad_host$pad_script?app=$app&page=$url&pad_include=1";

    $input ['cookies'] ['PADSESSID'] = $GLOBALS ['PADSESSID']; 
    $input ['cookies'] ['PADREQID']  = $GLOBALS ['PADREQID']; 

    pad_trace ( "pad_include", $input ['url'] );

    pad_curl_get ($input, $output);

    return $output;

  }

  function pad_curl ($url) {

    global $pad_host, $pad_script, $app;

    if ( substr($url, 0, 1) == '"' or substr($url, 0, 1) == "'" )
      $url = substr($url, 1, -1);

    $include = '';
    if ( substr($url, -14) == '&pad_include=1') {
      $include = '&pad_include=1';
      $url = substr($url, 0, -14);
    }

    pad_trace ("curl-1/start", $url);

    if ( pad_file_exists ( PAD_APP . "pages/$url.php" ) or pad_file_exists ( PAD_APP . "pages/$url.html" ) ) {
      return pad_curl_extra ("$pad_host$pad_script?app=$app&page=$url$include", $output);
    }
 
    if ( substr ( $url, 0, 7 ) == 'http://' or substr ( $url, 0, 8 ) == 'https://' ) {

      $data = pad_curl_extra ( "$url$include", $output );

    } elseif ( pad_get_check ( $url ) )

      $data = pad_get_content ( $url );

    else 

      return pad_error ("curl: invalid URL: $url");

    pad_trace ( "curl-1/end", $data);

    return $data;

  }

  function pad_curl_extra ($input, &$output) {

    //  Required input parms
    //  - ['url']
    
    //  Optional input parms
    //  - ['get']
    //  - ['post']
    //  - ['cookies']
    //  - ['headers']
    //  - ['options']

    $output                  = [];
    $output ['result_code']  = '999';  //  200 / 404 / etc
    $output ['result_type']  = '';     //  'xml' , 'html' , 'json' , 'yaml' , 'csv' , ''
    $output ['info']         = [];
    $output ['headers']      = [];
    $output ['cookies']      = [];
    $output ['data']         = '';

    if ( ! is_array($input) ) {
      $url = $input;
      $input = [];
      $input ['url'] = $url;
    }

    $url = $input ['url'] ?? '';

    if ( substr($url, 0, 1) == '"' or substr($url, 0, 1) == "'" )
      $url =  substr($url, 1, -1);
 
    if ( substr ( $url, 0, 7 ) == 'http://' or substr ( $url, 0, 8 ) == 'https://' )

      $data = pad_curl_get ( $input, $output );

    elseif ( pad_get_check ( $url, FALSE ) ) {
 
      $data    = pad_get_content ( $url );
      $content = '';
 
      pad_content_type ($data, $content);
 
      $output ['result_code']  = '200';
      $output ['result_type']  = $content; 
 
    }
 
    else
 
      $data = pad_curl_get ( $input, $output );

    return $data;

  }

  function pad_curl_get ( $input, &$output ) {

    pad_trace ("curl-2/start", $input ['url']);

    $url = $input ['url'];
    $error = FALSE;

    $get = '';
    if ( isset($input['get']) ) {
      $str = ( strpos ($input ['url'], '?' ) === FALSE ) ? '?' : '&';
      foreach ( $input['get'] as $key => $val ) {
        $get .= $str . $key . '=' . urlencode($val);
        $str = '&';
      }
    }

    $options = $input ['options'] ?? [];
 
    pad_curl_opt ($options, 'RETURNTRANSFER', true);
    pad_curl_opt ($options, 'ENCODING',       'gzip' );
    pad_curl_opt ($options, 'FOLLOWLOCATION', true);
    pad_curl_opt ($options, 'HEADER',         true);
    pad_curl_opt ($options, 'USERAGENT',      $_SERVER['HTTP_USER_AGENT'] ?? 'Mozilla/5.0 (X11; Linux x86_64) Gecko/20100101 Pad/1.0');
    pad_curl_opt ($options, 'REFERER',        $GLOBALS['pad_location'] . $GLOBALS['page']);

    if ( isset($input['user']) )
      pad_curl_opt ($options, 'USERPWD', $input['user'] . ":" . $input['$password']);
    
    $curl = curl_init ( $input ['url'] . $get );
    
    if ( isset($input['post']) ) {
      pad_curl_opt ($options, 'POST', true);
      pad_curl_opt ($options, 'POSTFIELDS', $input ['post']);
    }
  
    if ( isset($input['cookies']) ) {
      $cookies = '';
      foreach ( $input['cookies'] as $key => $val ) {
        if ($cookies)
          $cookies .= '; ';
        $cookies .= $key . '=' . $val;
      }
      pad_curl_opt ($options, 'COOKIE', $cookies);
    }
  
    if ( isset($input['headers']) ) {
      $headers_in = [];
      foreach ( $input['headers'] as $key => $val )
        $headers_in [] = $key . ': ' . $val;
      pad_curl_opt ($options, 'HTTPHEADER', $headers_in);
    }
  
    foreach ( $options as $key => $val )
      curl_setopt ( $curl, constant('CURLOPT_'.$key), $val );
  
    pad_timing_start ('curl');
    $result          = curl_exec    ($curl);
    $output ['info'] = curl_getinfo ($curl);
    pad_timing_end   ('curl');

    if ($result === FALSE) {
      pad_trace ("curl/error", "FALSE from curl_exec: $url");
      return '';
    }
    
    if ( isset ( $output ['info'] ['http_code'] ) )
      $output ['result_code'] = $output ['info'] ['http_code'];
    else
      $output ['result_code'] = 'xxx';

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
            $file = pad_between ($value, '"', '"');
        
          if ( $header == 'Content-Type' )
            if     (strpos ($value, 'html')       !== FALSE) $output ['result_type'] = 'html';
            elseif (strpos ($value, 'xml')        !== FALSE) $output ['result_type'] = 'xml';
            elseif (strpos ($value, 'json')       !== FALSE) $output ['result_type'] = 'json';
            elseif (strpos ($value, 'javascript') !== FALSE) $output ['result_type'] = 'json';
            elseif (strpos ($value, 'csv')        !== FALSE) $output ['result_type'] = 'csv';
            elseif (strpos ($value, 'yaml')       !== FALSE) $output ['result_type'] = 'yaml';
            elseif (strpos ($value, 'yml')        !== FALSE) $output ['result_type'] = 'yaml';
         
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

    if ( substr ($output ['info']['http_code'], 0, 1) <> '2' ) {

      pad_trace ("curl/error", "Geen 200 retour $url: " . $output ['info']['http_code'] );

      $error = TRUE;

    }

    if ( isset($output ['info']['header_size']) )
      $output ['data'] = trim(substr($result, $output ['info']['header_size']));
    else
      $output ['data'] = trim($result);

    if ( ! $output ['result_type'] and $file) {
      $pos = strrpos($file, '.');
      if ( ! $pos !==FALSE)
        $output ['result_type'] = substr($parm, $pos+1);
    }

    if ( ! $output ['result_type'] or $output ['result_type'] == 'json')
      pad_content_type ( $output ['data'], $output ['result_type'] );
 
    pad_trace ( "curl-2/end", $output ['info']['http_code'] . ' - ' . $output ['data']);

    if ( $error )
      return '';
    else
      return $output ['data'];
    
  }

  
  function pad_curl_opt (&$options, $name, $value) {
    
    if ( ! isset ( $options [$name] ) )
      $options [$name] = $value;

  }


?>