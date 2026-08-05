<?php

  // $padInfo selector 'info': the general-purpose combination - statistics, tracking and
  // cross-reference, but not the costly trace or xml dumps.
  //
  // Tracking is limited to the files the request touched; data and database tracking stay
  // off so a normal page stays cheap to profile.

  $padInfoStats = TRUE;
  $padInfoTrack = TRUE;
  $padInfoXref  = TRUE;

  $padInfoTrackFileRequest = TRUE;
  $padInfoTrackFileData    = FALSE;
  $padInfoTrackDbSession   = FALSE;
  $padInfoTrackDbRequest   = FALSE;
  $padInfoTrackDbData      = FALSE;

?>