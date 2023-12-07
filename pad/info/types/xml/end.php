<?php
 
  global $padInfoDir, $padXmlDetails, $padXmlTree;

  padXml     ();
  padXmlTidy ();

  if ( $padXmlDetails )
    padInfoPut ( "$padInfoDir/xml/tree.json" , $padXmlTree );

?>