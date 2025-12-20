<?php

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
