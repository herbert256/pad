<?php
  
  include_once 'info/types/xml/_lib.php';

  $padInfoXmlId     = 0;
  $padInfoXmlDepth  = 0;
  $padInfoXmlTree   = [];
  $padInfoXmlLevel  = [];
  $padInfoXmlEvents = [];

  if ( padInclude () )
    $padInfoXmlFile = APP . "_xml/include/$padStartPage.xml";
  else
    $padInfoXmlFile = APP . "_xml/complete/$padStartPage.xml";

  if ( $GLOBALS ['padInfoXmlCompact'] )
    $padInfoXmlFile = str_replace('_xml/', '_xml/compact/', $padInfoXmlFile); 
 
  if ( file_exists ( $padInfoXmlFile )  ) 
    unlink ( $padInfoXmlFile  );

?>