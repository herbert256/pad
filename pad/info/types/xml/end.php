<?php

  // Writes out the 'xml' info mode, from info/end/config.php: padInfoXml replays the event log
  // collected during the page against the node tree and appends the XML to $padInfoXmlFile,
  // after which padFileXmlTidy (pad/lib/tidy.php) re-indents it when $padInfoXmlTidy is set.
  //
  // Returns silently when the mode never started, so its helpers were never loaded.

  global $padInfoXmlFile, $padInfoXmlTidy;

   if ( ! function_exists ( 'padInfoXml') )
    return;

  padInfoXml ();

  if ( $padInfoXmlTidy )
   padFileXmlTidy ( $padInfoXmlFile );

?>