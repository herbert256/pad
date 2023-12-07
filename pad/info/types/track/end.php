<?php

  if ( $GLOBALS ['padTrackDbSession'] or $GLOBALS ['padTrackDbRequest'] )
    padTrackDbSession ();

  if (  $GLOBALS ['padTrackFileData'] )
    padTrackFileData ();

  if (  $GLOBALS ['padTrackDbData'] )
    padTrackDbData ();

  if ( $GLOBALS ['padTrackFileRequest'] )
    padTrackFileRequestEnd ();

?>