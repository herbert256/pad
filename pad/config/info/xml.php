<?php
  
  $padMain    = FALSE;  // Main information about a PAD request
  $padRequest = FALSE;  // Log the details of the HTTP(s) request 
  $padTrack   = FALSE;  // Big Brother, session and request information of the client
  $padXml     = TRUE;   // Build a XML file of the structure of the PAD page 
  $padXref    = FALSE;  // Build the <app>_xref and <data>xref directories
  $padStats   = FALSE;  // Keep runtime statistics about time and cpu used
  $padTrace   = FALSE;  // Trace the internal working of PAD

  $padXmlParms     = FALSE;
  $padXmlShowEmpty = FALSE;
  $padXmlTidy      = TRUE;

?>