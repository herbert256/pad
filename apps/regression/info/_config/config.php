<?php

  $padCommon = FALSE;

  // Four info modes by name, and then every option each of them honours, spelled out so the
  // reference sees each mode chosen. Not trace: it wrote some 90 files for every request of
  // this application, and every ./ci.sh run left them under DATA/trace/ - half a million
  // files nothing ever removed.

  $padInfo = 'stats,track,xml,xref';

  $padInfoTrackFileRequest = TRUE;
  $padInfoTrackFileData    = TRUE;
  $padInfoTrackDbSession   = TRUE;
  $padInfoTrackDbRequest   = TRUE;
  $padInfoTrackDbData      = TRUE;
  $padInfoXmlParms         = TRUE;
  $padInfoXmlTidy          = TRUE;
  $padInfoXmlCompact       = TRUE;

?>
