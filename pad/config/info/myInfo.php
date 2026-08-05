<?php

  // $padInfo selector 'myInfo': a personal preset, like info.php but with file data tracking
  // and the xml level dump added and trace still left off. Kept separate so the shared
  // 'info' preset can be tuned without disturbing it.

  $padInfoStats = TRUE;

  $padInfoTrack = TRUE;

  $padInfoTrackFileRequest = TRUE;
  $padInfoTrackFileData    = TRUE;
  $padInfoTrackDbSession   = FALSE;
  $padInfoTrackDbRequest   = FALSE;
  $padInfoTrackDbData      = FALSE;

  $padInfoXref = TRUE;

  $padInfoXml = TRUE;

?>