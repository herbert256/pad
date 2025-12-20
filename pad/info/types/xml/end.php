<?php

   if ( ! function_exists ( 'padInfoXml') )
    return;

  padInfoXml ();

  if ( $GLOBALS ['padInfoXmlTidy'] )
   padFileXmlTidy ( $GLOBALS ['padInfoXmlFile'] );

?>
