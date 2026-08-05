<?php

  // $padInfo selector 'track': enables the tracking info type (info/types/track/), which
  // logs the request to DATA. The sub-flags choose the destinations - here the per-request
  // and per-data files are written, while the three database logs (session, request, data)
  // stay off.

  $padInfoTrack = TRUE;

  $padInfoTrackFileRequest = TRUE;
  $padInfoTrackFileData    = TRUE;
  $padInfoTrackDbSession   = FALSE;
  $padInfoTrackDbRequest   = FALSE;
  $padInfoTrackDbData      = FALSE;

?>