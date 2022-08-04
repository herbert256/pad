<?php

  function pad_include ( $app, $page='index', $query='' ) {

    return pad ( $app, $page, $query, 1 );
    
  }

  function pad ( $app, $page='index', $query='', $include='' ) {

    $result = pad_complete ( $app, $page, $query, $include );

    return $result ['data'];
    
  }

  function pad_complete ( $app, $page='index', $query='', $include='' ) {

    global $pad_host, $pad_script;

    if ($include)
      $include = '&pad_include=1';

    $input = $output = [];

    $input ['url'] = "$pad_host$pad_script?app=$app&page=$page$query$include";

    $input ['cookies'] ['PADSESSID'] = $GLOBALS['PADSESSID'];
    $input ['cookies'] ['PADREQID']  = $GLOBALS['PADREQID'];
    
    return pad_curl ($input);
    
  }

  function pad_curl_data ($input) {    

    $curl = pad_curl ($input);
    
    return $curl ['data'];

  }

  function pad_curl ($input) {

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
        $output ['data']    = pad_file_get_contents ( $file );   
        $output ['type']    = pad_content_type  ( $output ['data'] );   
        $output ['result']  = '200';
      } else {
        $output ['data']    = '';      
        $output ['result']  = '404';
      }
      return $output;
    }

    $options = $input ['options'] ?? [];
 
    pad_curl_opt ($options, 'RETURNTRANSFER', true);
    pad_curl_opt ($options, 'ENCODING',       'gzip' );
    pad_curl_opt ($options, 'FOLLOWLOCATION', true);
    pad_curl_opt ($options, 'HEADER',         true);
    pad_curl_opt ($options, 'USERAGENT',      $_SERVER['HTTP_USER_AGENT'] ?? 'Mozilla/5.0 (X11; CrOS x86_64 13904.77.0) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.147 Safari/537.36 PAD/10.0');
    pad_curl_opt ($options, 'REFERER',        $GLOBALS['pad_location'] . $GLOBALS['page']);

    if ( isset($input['user']) )
      pad_curl_opt ($options, 'USERPWD', $input['user'] . ":" . $input['$password']);
    
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

    $output ['options'] = $options;      

    $curl = curl_init ( $input ['url'] );

    if ($curl === FALSE)
      return pad_curl_error ($output, 'curl_init = FALSE');

    foreach ( $options as $key => $val )
      curl_setopt ( $curl, constant('CURLOPT_'.$key), $val );
  
    pad_timing_start ('curl');
    $result          = curl_exec    ($curl);
    $output ['info'] = curl_getinfo ($curl);
    pad_timing_end   ('curl');

    if ($result  === FALSE)
      return pad_curl_error ($output, 'curl_exec = FALSE');
    
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
            $file = pad_between ($value, '"', '"');
        
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
      $pos = strrpos($file, '.');
      if ( ! $pos !==FALSE)
        $output ['type'] = substr($parm, $pos+1);
    }

    if ( ! $output ['type'] )
      $output ['type'] = pad_content_type ( $output ['data'] );
 
    if ($GLOBALS['pad_trace_curl'])
      pad_trace_curl ( $output );

    $GLOBALS['pad_curl_last'] = $output;

    return $output;
    
  }

  function pad_trace_curl ( $trace ) {

    $file = $GLOBALS ['pad_level_dir'] . "/curl_" . pad_random_string(). ".json";

    pad_file_put_contents ($file, pad_json ($trace) );

  }
  
  function pad_curl_opt (&$options, $name, $value) {
    
    if ( ! isset ( $options [$name] ) )
      $options [$name] = $value;

  }

  function pad_curl_error ($output, $error) {

    $output ['ERROR'] = $error;

     if ($GLOBALS['pad_trace_curl'])
      pad_trace_curl ( $output );

    $GLOBALS['pad_curl_last'] = $output;

    return $output;

  }

?>