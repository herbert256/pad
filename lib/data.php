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
    
    if ( ! $content )
      $content = padContentType ($data);

    $result = include pad . "data/$content.php";
    
    return padDataChk ( $result, $name );

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

    global $pad, $padPrm, $padTag, $padName, $padForceDataName;
    
    if     ( $name                              ) $return = $name;
    elseif ( $padForceDataName                  ) $return = $padForceDataName;
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
  
    return include pad . 'types/go/local.php';

  }
  

?>