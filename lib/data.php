<?php

  function padData ($input, $content='', $name='') {

    if     ( $input === NULL           ) $data = [];
    elseif ( $input === FALSE          ) $data = [];
    elseif ( $input === NAN            ) $data = [];
    elseif ( $input === INF            ) $data = [];
    elseif ( $input === TRUE           ) $data = [ 1 => [] ];
    elseif ( is_array ( $input)        ) $data = $input;
    elseif ( is_object ( $input)       ) $data = padToArray ( $input );
    elseif ( is_resource ( $input)     ) $data = padToArray ( $input );
    elseif ( ! $input                  ) $data = [];
    elseif ( strlen(trim($input)) == 0 ) $data = [];
    else                                 $data = trim ( $input );

    if ( is_array ( $data ) )
      return padDataChk ($data, $name);

    $check = padDataFileName ( $data );
    if ( $check )
      return padDataFileData ( $check );

    if ( str_starts_with ( strtolower ( $data ), 'http:' ) or str_starts_with ( strtolower ( $data ), 'https:' )  ) {

      $curl = padCurl ($data);
 
      if ( str_starts_with ( $curl ['result'],  '2' ) )
        return padData ( $curl [$data], $curl ['type'], $name );
      else
        return [];    
 
    }

    if ( padCheckRange ( $data ) ) { 
      $data = padGetRange ( $data );
      return padDataChk ($data, $name);
    }

    if ( substr($data, 0, 1) == '(' and substr($data, -1) == ')' ) {

      $data = padExplode(substr($data, 1, -1), ',');

      foreach ($data as $key => $value)
        $data[$key] = padEval($value);

      return padDataChk ($data, $name);

    }
    
    if ( ! $content )
      $content = padContentType ($data);

    if ( ! $content )
      $content = 'csv';

    $csv = $html = $xml = $json = $yaml = '';
    
    if     ( $content == 'csv' )  $csv  = $data;
    elseif ( $content == 'html')  $html = $data;
    elseif ( $content == 'xml' )  $xml  = $data;
    elseif ( $content == 'json')  $json = $data;
    elseif ( $content == 'yaml')  $yaml = $data;
    else                          return padDataError ($data, "Content type '$content' not supported");

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
        $header [$key1] = trim(str_replace('!!Q!!', '"', urldecode($val1)));
   
      foreach ($lines as $key1 => $val1)
        if ($key1 > 0)
          foreach (explode(',', $val1) as $key2 => $val2)
            $result [$key1] [$header[$key2]] = trim(str_replace('!!Q!!', '"', urldecode($val2)));

      if ( ! is_array($result) or $result === NULL or $result === FALSE)
        return padDataError ($data, "CSV conversion error");

    }

    if ( $html ) {
    
      $tidyoptions = [
        'output-xml'   => true,
        'force-output' => true
      ];

      $xml = new tidy;
      $xml->parseString($data, $tidyoptions, 'utf8');
      $xml->cleanRepair();

      if ( $xml === FALSE )
        return padDataError ($data, "TIDY conversion error (html)");
  
    }
  
    if ( $xml ) {

      $xml = str_replace('&nbsp;', ' ', $xml);
      
      libxml_use_internal_errors (true);
      $simple_xml = simplexml_load_string($xml, "SimpleXMLElement", LIBXML_NOERROR | LIBXML_ERR_NONE);
    
      if ( $simple_xml === FALSE and ! $html ) {
        
         $tidyoptions = [
          'input-xml'    => true,
          'output-xml'   => true,
          'force-output' => true
        ];
        
        $xml = new tidy;
        $xml->parseString($xml, $tidyoptions, 'utf8');
        $xml->cleanRepair();
        
        if ( $xml === FALSE )
          return padDataError ($data, "TIDY conversion error (xml)");
      
        libxml_use_internal_errors (true);
        $simple_xml = simplexml_load_string ($xml, "SimpleXMLElement", LIBXML_NOERROR | LIBXML_ERR_NONE);
        
      }

      if ($simple_xml === FALSE)
        return padDataError ($data, "XML conversion error");
  
      $json = json_encode($simple_xml, JSON_PARTIAL_OUTPUT_ON_ERROR);
      
      if ( $json === NULL or $json === FALSE )
        return padDataError ($data, "JSON error (encode): " . json_last_error() . ' - ' . json_last_error_msg() );
      
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
        return padDataError ($data, "JSON conversion error");

      $result = json_decode($json, true);
      
      if ( ! is_array($result) or $result === NULL or $result === FALSE)
        return padDataError ($data, "JSON error (decode): " . json_last_error() . ' - ' . json_last_error_msg() );

    }

    if ( $yaml ) {

      $result = yaml_parse ($yaml);

      if ( ! is_array($result) or $result === NULL or $result === FALSE)
        return padDataError ($data, "YAML parse error" );
      
    }

    $result = padDataChk ($result, $name);

    return $result;

  }
  
  
  function padDataError ($data, $error) {

    return padError($error);
    
  }


  function padDataForcePad ($data) {

    $result = [];

    foreach ( $data as $name => $value) {
$result [$name] ['name'] = $name;      
$result [$name] ['value'] = $value;      
    }

return $result;
  }


  function padDataChk ($data,$name) {

    $result = $data;

    if ( ! is_array ($result) )
      return padDefaultData ();

    if ( padIsDefaultData($result) or ! count($result) )
      return $result;

    $result = padDataChkSimpleArray ($result,$name);
    $result = padDataChkChkOne      ($result,$name);
    $result = padDataChkDataAttr    ($result,$name);
    $result = padDataChkCheckRecord ($result,$name); 
    $result = padDataChkCheckArray  ($result,$name);

    return $result;

  }


  function padDataChkSimpleArray ($data,$name) {

    $result = $data;

    foreach ($result as $padK => $padV)
      if ( is_array($padV) or ! is_numeric($padK) )
        return $result;
  
    $name   = padDataName($name);
    $tmp    = $result;
    $result = [];
    
    foreach ($tmp as $k => $v)
      $result [$k] [$name] = $v;

    return $result;

  }
  

  function padDataChkCheckArray ($data,$name) {

    $result = $data;

    foreach ($result as $k => $v) {
      $x = 0 ;
      foreach ($v as $k2 => $v2)
        if ( ctype_digit( (string) $k2) and ( $k2 == $x or ($k2-1==$x) ) and ! is_array($v2) )
          $x++;
        else
          return $result;
    }

    $name = padDataName($name);

    foreach ($result as $k => $v) {
      $tmp = $v;
      $result [$k] = [];
      foreach ($tmp as $k2 => $v2)
        $result [$k] ["$name"] [$k2] ["$name"] = $v2;
    }

    return $result;

  }


  function padDataChkCheckRecord ($data,$name) {

    $result = $data;
    
    foreach ($result as $k => $v)
      if ( ! is_array($v) ) {
        $tmp = $result;
        $result = [];
        foreach ($tmp as $k => $v)
          $result [0] [$k] = $v;
        return $result;
      }

    return $result;

  }


  function padDataChkChkOne ($data,$name) {

    $result = $data;

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
    
    return $result;

  }


  function padDataChkDataAttr ($data,$name) {

    $result = $data;
    
    foreach ($result as $k => $v)
      if ( is_array($v) )
        if (trim($k) == 'attr') {
          foreach ($v as $k2 => $v2)
            $result [$k2] = $v2;
          unset ($result [$k]);
        } else
          $result [$k] = padDataChkDataAttr ( $result [$k], $name );

    return $result;

  }


  function padDataName ($name) {

    global $pad, $padPrm, $padTag, $padName;
    
    if     ( $name                              ) $return = $name;
    elseif ( isset ($padPrm [$pad] ['name'] )   ) $return = $padPrm [$pad] ['name'];
    elseif ( isset ($padPrm [$pad] ['toData'] ) ) $return = $padPrm [$pad] ['toData'];
    elseif ( $padTag [$pad] == 'data '          ) $return = $padOpt [$pad] [1];
    elseif ( isset ($padTag [$pad] )            ) $return = $padTag [$pad];

    if ( substr($return, 0, 1) == '$' )
      $return = substr($return, 1);

    return $return;

  }


  function padInclFileName ( $check ) {

    foreach ( padDirs () as $key => $value ) {

      $file = substr (padApp, 0, -1) . $value . "_includes/$check";

      if ( padExists ($file) and ! is_dir($file) ) return $file;
      if ( padExists ("$file.php")               ) return "$file.php";
      if ( padExists ("$file.html")              ) return "$file.html";

    }

    return '';

  }


  function padDataFileName ( $check ) {

    foreach ( padDirs () as $key => $value ) {

      $file = substr (padApp, 0, -1) . $value . "_data/$check";

      if ( padExists ($file) and ! is_dir($file) ) return $file;
      if ( padExists ("$file.xml")               ) return "$file.xml";
      if ( padExists ("$file.json")              ) return "$file.json";
      if ( padExists ("$file.yaml")              ) return "$file.yaml";
      if ( padExists ("$file.csv")               ) return "$file.csv";
      if ( padExists ("$file.php")               ) return "$file.php";

    }

    return '';

  }


 function padDataFileData ( $padLocalFile ) {
  
    return include 'types/go/local.php';

  }
  

?>