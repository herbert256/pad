<?php
  
  include_once pad . 'info/types/xml/lib.php';

  $padXmlId     = 0;
  $padXmlDepth  = 0;
  $padXmlTree   = [];
  $padXmlLevel  = [];
  $padXmlEvents = [];

  if ( file_exists ( padData . "xml/$padStartPage.xml" ) )
    unlink ( padData . "xml/$padStartPage.xml" ) ;

?>