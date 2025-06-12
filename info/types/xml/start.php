<?php
  
  include_once 'info/types/xml/_lib.php';

  $padInfoXmlId     = 0;
  $padInfoXmlDepth  = 0;
  $padInfoXmlTree   = [];
  $padInfoXmlLevel  = [];
  $padInfoXmlEvents = [];

  if ( isset ( $_REQUEST ['padInclude'] ) or ( isset ( $GLOBALS ['padInclude'] ) and $GLOBALS ['padInclude'] ) )
    $padInfoXmlFile = APP . "_xml/include/$padStartPage.xml";
  else
    $padInfoXmlFile = APP . "_xml/complete/$padStartPage.xml";

  if ( file_exists ( $padInfoXmlFile )  ) 
    unlink ( $padInfoXmlFile  );

?>