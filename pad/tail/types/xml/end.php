<?php
 
 global $padTailDir, $padTailMetaNoXml, $padXmlDetails, $padXmlTree;

  if ( $padTailMetaNoXml )
    return;

  padXml     ();
  padXmlTidy ();

  if ( $padXmlDetails )
    padTailPut ( "$padTailDir/xml/tree.json" , $padXmlTree );

?>