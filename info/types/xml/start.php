<?php
  
  include_once 'info/types/xml/_lib.php';

  $padInfoXmlId     = 0;
  $padInfoXmlDepth  = 0;
  $padInfoXmlTree   = [];
  $padInfoXmlLevel  = [];
  $padInfoXmlEvents = [];

  if ( isset ( $_REQUEST['padInclude'] ) )
    $padInfoXmlDir = "xml/include/$padStartPage";
  else
    $padInfoXmlDir = "xml/complete/$padStartPage";

  if ( file_exists ( DAT . "$padInfoXmlDir/long.xml" )  ) unlink ( DAT . "$padInfoXmlDir/long.xml"  ) ;
  if ( file_exists ( DAT . "$padInfoXmlDir/short.xml" ) ) unlink ( DAT . "$padInfoXmlDir/short.xml" ) ;

?>