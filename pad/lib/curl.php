<?php

  function pad_curl ($input, &$output) {

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

    $output                  = [];
    $output ['input']        = $input;
    $output ['options']      = [];
    $output ['result_code']  = '999';  //  200 / 404 / etc
    $output ['result_type']  = '';     //  'xml' , 'html' , 'json' , 'yaml' , 'csv' , ''
    $output ['info']         = [];
    $output ['headers']      = [];
    $output ['cookies']      = [];
    $output ['data']         = '';

    $error = FALSE;

    if ( isset($input['get']) ) {
      $str = ( strpos ($input ['url'], '?' ) === FALSE ) ? '?' : '&';
      foreach ( $input['get'] as $key => $val ) {
        $input ['url'] .= $str . $key . '=' . urlencode($val);
        $str = '&';
      }
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
    foreach ( $options as $key => $val )
      curl_setopt ( $curl, constant('CURLOPT_'.$key), $val );
  
    pad_timing_start ('curl');
    $result          = curl_exec    ($curl);
    $output ['info'] = curl_getinfo ($curl);
    pad_timing_end   ('curl');

    if ($result === FALSE) {
      if ($GLOBALS['pad_trace_curl'])
        pad_trace_curl ( $output  );
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

    if ( substr ($output ['info']['http_code'], 0, 1) <> '2' )
      $error = TRUE;

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
 
    if ($GLOBALS['pad_trace_curl'])
      pad_trace_curl ( $output );

    if ( $error )
      return '';
    else
      return $output ['data'];
    
  }

  function pad_trace_curl ( $trace ) {

    pad_trace_write ( 'curl', pad_random_string(). ".json", pad_json ($trace) );

  }
  
  function pad_curl_opt (&$options, $name, $value) {
    
    if ( ! isset ( $options [$name] ) )
      $options [$name] = $value;

  }


?>