<?php

  if ( ! function_exists ( 'padInfoTrackDbSession') )
    return;

  if ( $GLOBALS ['padInfoTrackDbSession'] or $GLOBALS ['padInfoTrackDbRequest'] )
    padInfoTrackDbSession ();

  if (  $GLOBALS ['padInfoTrackDbData'] )
    padInfoTrackDbData ();

  if ( $GLOBALS ['padInfoTrackFileRequest'] )
    padInfoTrackEnd ();

  if ( $GLOBALS ['padInfoTrackFileData'] )
    padInfoTrackData ();

?>