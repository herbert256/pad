<?php
  
  include_once '/pad/info/types/xml/_lib.php';

  $padInfoXmlId     = 0;
  $padInfoXmlDepth  = 0;
  $padInfoXmlTree   = [];
  $padInfoXmlLevel  = [];
  $padInfoXmlEvents = [];

  if ( isset ( $_REQUEST['padInclude'] ) )
    $padInfoXmlDir = "xml/include/$padStartPage";
  else
    $padInfoXmlDir = "xml/complete/$padStartPage";

  if ( file_exists ( "/data/$padInfoXmlDir/long.xml" )  ) unlink ( "/data/$padInfoXmlDir/long.xml"  ) ;
  if ( file_exists ( "/data/$padInfoXmlDir/short.xml" ) ) unlink ( "/data/$padInfoXmlDir/short.xml" ) ;

?>