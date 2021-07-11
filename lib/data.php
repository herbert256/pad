<?php

  function pad_make_data ($input, $content='', $name='') {

    if     ( $input === NULL       )  $data = [];
    elseif ( $input === FALSE      )  $data = [];
    elseif ( $input === TRUE       )  $data = [1 => [] ];
    elseif ( is_array ( $input)    )  $data = $input;
    elseif ( is_object ( $input)   )  $data = pad_xxx_to_array ( $input );
    elseif ( is_resource ( $input) )  $data = pad_xxx_to_array ( $input );
    elseif ( ! $input              )  $data = [];
    else                              $data = (string) trim($input);

    if ( is_array ( $data ) ) {
      pad_data_chk ( $data, $name );
      return $data;
    }
 
    if ( pad_get_check ( $data ) )
      return pad_get_data ( $data );

    if ( pad_check_range ( $input ) ) { 

      $data = pad_get_range ( $input );

      pad_data_chk ( $data, $name );
 
      return $data;
 
    }

    if ( substr($data, 0, 1) == '(' and substr($data, -1) == ')' ) {

      $data = pad_explode(substr($data, 1, -1), ',');

      foreach ($data as $key => $value)
        $data[$key] = pad_eval($value);

      pad_data_chk ( $data, $name );

      return $data;

    }
    
    if ( ! $content )
      pad_content_type ($data, $content);

    $csv = $html = $xml = $json = $yaml = '';
    
    if     ( $content == 'csv' )  $csv  = $data;
    elseif ( $content == 'html')  $html = $data;
    elseif ( $content == 'xml' )  $xml  = $data;
    elseif ( $content == 'json')  $json = $data;
    elseif ( $content == 'yaml')  $yaml = $data;
    elseif ( $content == '')      return pad_data_error ($data, "Not be able to autodetect content type: $data");
    else                          return pad_data_error ($data, "Content type '$content' not supported");

    $result = [];
  
    if ( $csv ) {
      
      $enc = preg_replace_callback(
          '/"(.*?)"/s',
          function ($field) {
              return urlencode(utf8_encode($field[1]));
          },
          preg_replace('/(?<!")""/', '!!Q!!', trim($csv))
      );
      
      $lines  = preg_split('/( *\R)+/s', $enc);
      $header = explode(',', $lines [0]);
   
      foreach ($header as $key1 => $val1)
        $header [$key1] = trim(str_replace('!!Q!!', '"', utf8_decode(urldecode($val1))));
   
      foreach ($lines as $key1 => $val1)
        if ($key1 > 0)
          foreach (explode(',', $val1) as $key2 => $val2)
            $result [$key1] [$header[$key2]] = trim(str_replace('!!Q!!', '"', utf8_decode(urldecode($val2))));

      if ( ! is_array($result)  or $result === NULL or $result === FALSE)
        return pad_data_error ($data, "CSV conversion error");

    }

    if ( $html ) {
    
      $tidy_options = [
        'output-xml'   => true,
        'force-output' => true
      ];

      $xml = new tidy;
      $xml->parseString($data, $tidy_options, 'utf8');
      $xml->cleanRepair();

      if ( $xml === FALSE )
        return pad_data_error ($data, "TIDY conversion error (html)");
  
    }
  
    if ( $xml ) {

      $xml = str_replace('&nbsp;', ' ', $xml);
      
      libxml_use_internal_errors (true);
      $simple_xml = simplexml_load_string($xml, "SimpleXMLElement", LIBXML_NOERROR | LIBXML_ERR_NONE);
    
      if ( $simple_xml === FALSE and ! $html ) {
        
         $tidy_options = [
          'input-xml'    => true,
          'output-xml'   => true,
          'force-output' => true
        ];
        
        $xml = new tidy;
        $xml->parseString($xml, $tidy_options, 'utf8');
        $xml->cleanRepair();
        
        if ( $xml === FALSE )
          return pad_data_error ($data, "TIDY conversion error (xml)");
      
        libxml_use_internal_errors (true);
        $simple_xml = simplexml_load_string ($xml, "SimpleXMLElement", LIBXML_NOERROR | LIBXML_ERR_NONE);
        
      }

      if ($simple_xml === FALSE)
        return pad_data_error ($data, "XML conversion error");
  
      $json = json_encode($simple_xml, JSON_PARTIAL_OUTPUT_ON_ERROR);
      
      if ( $json === NULL or $json === FALSE )
        return pad_data_error ($data, "JSON error (encode): " . json_last_error() . ' - ' . json_last_error_msg() );
      
      $json = str_replace('{"@attributes":{', '{"attr":{', $json);
      
    }
  
    if ( $json ) {
      
      $json = str_replace(['&open;', '&close;'], ['{', '}'], $json );

      $first1 = strpos  ($json, '{');
      $last1  = strrpos ($json, '}');

      $first2 = strpos  ($json, '[');
      $last2  = strrpos ($json, ']');

      if ($first1 !== FALSE and $last1 !== FALSE and ($first2 === FALSE or $first1 < $first2) )
        $json = substr($json, $first1, ($last1-$first1)+1);
      elseif ($first2 !== FALSE and $last2 !== FALSE and ($first1 === FALSE or $first2 < $first1) )
        $json = substr($json, $first2, ($last2-$first2)+1);
      else
        return pad_data_error ($data, "JSON conversion error");

      $result = json_decode($json, true);
      
      if ( ! is_array($result) or $result === NULL or $result === FALSE)
        return pad_data_error ($data, "JSON error (decode): " . json_last_error() . ' - ' . json_last_error_msg() );

    }

    if ( $yaml ) {

      $result = yaml_parse ($yaml);

      if ( ! is_array($result) or $result === NULL or $result === FALSE)
        return pad_data_error ($data, "YAML parse error" );
      
    }

    pad_data_chk ($result, $name);

    return $result;
    
  }
  
  
  function pad_data_error ($data, $error) {

    $GLOBALS['DEBUG_DATA_PAD'] = $data;

    return [];
    
  }

   function pad_data_chk (&$result,$name) {

    if ( ! count($result) )
      return;

    pad_data_chk_simple_array ($result,$name);
    pad_data_chk_chk_one      ($result,$name);
    pad_data_chk_data_attr    ($result,$name);
    pad_data_chk_check_record ($result,$name);
    pad_data_chk_check_array  ($result,$name);

  }


  function pad_data_chk_check_array (&$result,$name) {

    foreach ($result as $k => $v) {
      $x = 0 ;
      foreach ($v as $k2 => $v2)
        if ( ctype_digit( (string) $k2) and ( $k2 == $x or ($k2-1==$x) ) and ! is_array($v2) )
          $x++;
        else
          return;
    }

    $name = pad_data_name($name);

    foreach ($result as $k => $v) {
      $tmp = $v;
      $result [$k] = [];
      foreach ($tmp as $k2 => $v2)
        $result [$k] ["$name"] [$k2] ["$name"] = $v2;
    }

  }


  function pad_data_chk_check_record (&$result,$name) {
    
    foreach ($result as $k => $v)
      if ( ! is_array($v) ) {
        $tmp = $result;
        $result = [];
        foreach ($tmp as $k => $v)
          $result [0] [$k] = $v;
        return;
      }

  }

  
  function pad_data_chk_simple_array (&$result,$name) {

    foreach ($result as $pad_k => $pad_v)
      if ( is_array($pad_v) or ! is_numeric($pad_k) )
        return;
  
    $name   = pad_data_name($name);
    $tmp    = $result;
    $result = [];
    
    foreach ($tmp as $k => $v)
      $result [$k] [$name] = $v;

  }


  function pad_data_chk_chk_one (&$result,$name) {

    if ( count($result) == 1 and is_array($result[array_key_first($result)]) ) {
      
      $idx=0;
      foreach ($result[array_key_first($result)] as $key => $value) {
        if ( $key <> $idx ) {
          $idx = 0;
          break;
        }
        $idx++;
      }
      
      if ($idx) {
        $tmp = $result[array_key_first($result)];
        $result = $tmp;
      }

    }
    
  }


  function pad_data_chk_data_attr (&$result,$name) {
    
    foreach ($result as $k => $v)
      if ( is_array($v) )
        if (trim($k) == 'attr') {
          foreach ($v as $k2 => $v2)
            $result [$k2] = $v2;
          unset ($result [$k]);
        } else
          pad_data_chk_data_attr ( $result [$k], $name );

  }


  function pad_data_name ($name) {

    if     ( $name                          ) $return = $name;
    elseif ( $GLOBALS['pad_name'] == 'data' ) $return = $GLOBALS['pad_parm'];
    elseif ( pad_tag_parm ('name')          ) $return = pad_tag_parm ('name');
    elseif ( pad_tag_parm ('toData')        ) $return = pad_tag_parm ('toData');
    else                                      $return = $GLOBALS['pad_name'];

    if (substr($return, 0, 1) == '$')
      $return = substr($return, 1);

  }

  
?>