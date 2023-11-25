<?php
 
  global $padTailDir, $padXmlDetails, $padXmlTree;

  padXml     ();
  padXmlTidy ();

  if ( $padXmlDetails )
    padTailPut ( "$padTailDir/xml/tree.json" , $padXmlTree );

?>