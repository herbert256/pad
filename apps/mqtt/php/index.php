<?php

  $mqtt = new phpMQTT("localhost", 11883, "PHP_IIB_Publish");

  if ( ! $mqtt->connect() )
    pad_error("No connection with the MQTT server possible");

  $var = [
    [ 'name' => 'bob',   'phone' => '555-3425' ],
    [ 'name' => 'jim',   'phone' => '555-4364' ],
    [ 'name' => 'joe',   'phone' => '555-3422' ],
    [ 'name' => 'jerry', 'phone' => '555-4973' ]
  ];

  $mqtt->publish("PHP/TO/IIB/topic", varToXml('var', $var));

  $topics['IIB/TO/PHP/#'] = array("qos"=>0, "function"=>"procmsg");

  $mqtt->subscribe($topics,0);

  $mqtt->close();

  function procmsg($topic,$msg){
    return;
  }


  echo "klaar";

  function varToXml ($name, $var) {
    $xml = new SimpleXMLElement("<?xml version=\"1.0\" encoding=\"UTF-8\"?><$name/>");
      if ( is_array($var) and isNumArray($var) ) {
        $xml->addAttribute ('rows', count($var));
        varToXmlWalk ($var, 'row', $xml);
      }
      else {
        varToXmlWalk ($var, $name, $xml);
      }
    return $xml->asXML();
  }

  function varToXmlWalk ($var, $name, &$xml) {
    if(is_array($var))
      foreach ($var as $key => $value) {
          if(is_array($value)){
            $idx = is_int($key) ? $name : $key;
            $child = $xml->addChild($idx);
            if (isNumArray($value)) {
              $child->addAttribute ('rows', count($value));
              varToXmlWalk ($value, 'row', $child);
            }
            else {
              varToXmlWalk ($value, $idx, $child);
            }
          }
          else {
            $idx = is_int($key) ? $name : $key;
            $xml->addChild($idx, $value);
          }
      }
    else {
      $xml->addChild($name, $var);
    }
  }

  function isNumArray($array) {
    foreach ($array as $key => $value)
      if (!is_int($key))
        return FALSE;
    return TRUE;
  }

?>
