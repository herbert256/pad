<?php

  $mqtt = new phpMQTT("localhost", 1883, "IIB_TO_PHP_Subcribe");

  if ( ! $mqtt->connect() )
    pad_error("No connection with the MQTT server possible");

  $topics['PHP/TO/IIB/topic'] = array("qos"=>0, "function"=>"procmsg");

  $mqtt->subscribe($topics,1);

  $mqtt->close();

  function procmsg($topic,$msg){
    return;
  }

  echo "Done: IIB_TO_PHP_Subcribe";

?>
