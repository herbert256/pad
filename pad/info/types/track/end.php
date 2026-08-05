<?php

  // Files the finished request for the 'track' info mode, each destination behind its own switch:
  //
  //   $padInfoTrackDbSession / DbRequest  rows in the track_session and track_request tables
  //   $padInfoTrackDbData                 the response body in track_data, keyed on its etag
  //   $padInfoTrackFileRequest            request plus response as DATA/track/requests/<log>.json
  //   $padInfoTrackFileData               the plain page under DATA/track/data/
  //
  // Called from info/end/config.php; returns without doing anything when the helpers of
  // info/types/track/start.php were never loaded.

  global $padInfoTrackDbData, $padInfoTrackDbRequest, $padInfoTrackDbSession, $padInfoTrackFileData, $padInfoTrackFileRequest;

  if ( ! function_exists ( 'padInfoTrackDbSession') )
    return;

  if ( $padInfoTrackDbSession or $padInfoTrackDbRequest )
    padInfoTrackDbSession ();

  if (  $padInfoTrackDbData )
    padInfoTrackDbData ();

  if ( $padInfoTrackFileRequest )
    padInfoTrackEnd ();

  if ( $padInfoTrackFileData )
    padInfoTrackData ();

?>