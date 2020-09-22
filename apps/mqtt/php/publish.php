<?php

  $var ['tst_1'] = 'abc';

  $var ['tst_2'] ['abc'] = 'xyz';

  $var ['tst_3'] = ['abc'];

  $var ['tst_4'] = ['abc', 'xyz'];

  $var ['users'] = [
    [ 'name' => 'bob',   'phone' => '555-3425' ],
    [ 'name' => 'jim',   'phone' => '555-4364' ],
    [ 'name' => 'joe',   'phone' => '555-3422' ],
    [ 'name' => 'jerry', 'phone' => '555-4973' ]
  ];
  
  $var ['abc'] = 'abc';
  $var ['klm'] = [1,2,3,4];
  $var ['xyz'] = [];

  $var [77] = 123456;
  $var ["88"] = 123456;
  $var [99] = [55=>123456];
 
  $var ['leeg'] = '';
  $var ['nul'] = 0;
  $var ['true'] = TRUE;
  $var ['false'] = FALSE;
  
  $var ['trouble']['*']   = 'abc';
  $var ['trouble']['a-a'] = 'abc';
  $var ['trouble']['a_a'] = 'abc';
  $var ['trouble']['a<a'] = 'b<b';
  $var ['trouble']['a>a'] = 'b>b';
  $var ['trouble']['a&a'] = 'b&b';
  $var ['trouble']['a"a'] = 'b"b';
  $var ['trouble']["a'a"] = "b'b";
  $var ['trouble']["a\\a"] = "b\\b";
  
  $var [1][2][3][4][5][6][7][8][9][10][11][12][13][14][15][16][17][18][19] = $var ['users'];

  if ($type == 'x') {
    header ( 'Content-type: text/xml' );
    echo encode_xml ($var, 'abc');
  } elseif ($type == 'j') {
    header ( 'Content-type: application/json' );
    echo json_encode ($var, JSON_PRETTY_PRINT);
  } else {
    header ( 'Content-type: application/json' );
    echo encode_json ($var, 'abc');
  }

  exit;

  $mqtt = new phpMQTT("localhost", 1883, "PHP_TO_IIB");

  if ( ! $mqtt->connect() )
    pad_error("No connection with the MQTT server possible");


  $mqtt = new phpMQTT("localhost", 1883, "PHP_TO_IIB_JSON");
  if ( ! $mqtt->connect() )
    pad_error("No connection with the MQTT server possible");
  $mqtt->publish("PHP/JSON", $json);
  $mqtt->close();

  sleep(1);

  $mqtt = new phpMQTT("localhost", 1883, "PHP_TO_IIB_XML");
  if ( ! $mqtt->connect() )
    pad_error("No connection with the MQTT server possible");
  $mqtt->publish("PHP/XML",  $xml);
  $mqtt->close();

  echo "Done: PHP_TO_IIB_Publish";

  function encode_xml ($var,$root='root') {
    $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\r\n";
    encode_xml_2 ($root, $var, $xml, false);
    return $xml;
  }

  function encode_xml_2 ($name, $var, &$xml, $key) {
    $xml .= "<$name";
    if ($key!==false)
      $xml .= ' key="' . htmlentities($key, ENT_COMPAT | ENT_XML1) . '"';
    $xml .= ">";
    if (!is_array($var) and !is_object($var))
      $xml .= htmlentities($var, ENT_COMPAT | ENT_XML1);
    else
      foreach ($var as $key => $val)
        if ( preg_match('/^(?!XML)[a-z][\w0-9-]*$/i', $key))
          encode_xml_2 ($key, $val, $xml, false);
        else
          encode_xml_2 ('row', $val, $xml, $key);
    $xml .= "</$name>";
  }

  function encode_json ($var,$root='root') {
    $json = "{\n";
    encode_json_2 ($root, $var, $json, 0, 0, 0);
    $json .= "\n}";
    return $json;
  }

  function encode_json_2 ($name, $var, &$json, $cnt, $now, $lvl) {
    $lvl++;
    $json .= str_repeat ("    ", $lvl) .'"' . json_escape($name) . '":';
    if ( !is_array($var) and !is_object($var)) {
      $json .= '"' . json_escape($var) . '"';
      if ($now < $cnt)
        $json .= ",\n";
    } else {
    $json .= " {\n";
    $now2 = 0;
    foreach ($var as $key => $val) {
      $now2++;
      encode_json_2 ($key, $val, $json, count($var), $now2, $lvl);
    }
    $json .= "\n" . str_repeat ("    ", $lvl) . "}";
    if ($now < $cnt)
      $json .= ",\n";
    else
      $json .= "\n";
    }
  }

  function json_escape ($val) {
    return(str_replace('"', "\\".'"',str_replace("\\", "\\\\", $val)));
  }
 
?>