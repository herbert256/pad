<?php

  global $padInfoXmlFile, $padInfoXmlTidy;

   if ( ! function_exists ( 'padInfoXml') )
    return;

  padInfoXml ();

  if ( $padInfoXmlTidy )
   padFileXmlTidy ( $padInfoXmlFile );

?>