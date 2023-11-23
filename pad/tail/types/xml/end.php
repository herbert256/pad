<?php
 
  if ( $GLOBALS ['padTailNoXml'] )
    return;

  padXml     ();
  padXmlTidy ();

  if ( $GLOBALS ['padXmlDetails'] )
    padTailPut ( $GLOBALS ['padXmlDir'] . '/' . $GLOBALS ['padTailId'] . '/tree.json' , $GLOBALS ['padXmlTree'] );

?>