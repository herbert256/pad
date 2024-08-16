<?php

  if ( ! function_exists ( 'padTrackDbSession') )
    return;

  if ( $GLOBALS ['padTrackDbSession'] or $GLOBALS ['padTrackDbRequest'] )
    padTrackDbSession ();

  if (  $GLOBALS ['padTrackDbData'] )
    padTrackDbData ();

  if ( $GLOBALS ['padTrackFileRequest'] )
    padTrackEnd ();

?>