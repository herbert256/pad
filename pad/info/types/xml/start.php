<?php
  
  include_once pad . 'info/types/xml/_lib.php';

  $padXmlId     = 0;
  $padXmlDepth  = 0;
  $padXmlTree   = [];
  $padXmlLevel  = [];
  $padXmlEvents = [];

  if ( isset ( $_REQUEST['padInclude'] ) )
    $padXmlDir = "xml/include/$padStartPage";
  else
    $padXmlDir = "xml/complete/$padStartPage";

  if ( file_exists ( padData . "$padXmlDir/long.xml" )  ) unlink ( padData . "$padXmlDir/long.xml"  ) ;
  if ( file_exists ( padData . "$padXmlDir/short.xml" ) ) unlink ( padData . "$padXmlDir/short.xml" ) ;

?>