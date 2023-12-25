<?php

  if ( $GLOBALS ['padStats']   ) include pad . 'info/types/stats/end.php';
  if ( $GLOBALS ['padTrack']   ) include pad . 'info/types/track/end.php';
  if ( $GLOBALS ['padXml']     ) include pad . 'info/types/xml/end.php';
  if ( $GLOBALS ['padTrace']   ) include pad . 'info/types/trace/end.php';
  if ( $GLOBALS ['padRequest'] ) include pad . 'info/types/request/end.php';
  if ( $GLOBALS ['padXref']    ) include pad . 'info/types/xref/end.php';

  if ( isset ( $padInfoDir ) and file_exists ( $padInfoDir ) )           
    padDumpToDir ( '', "$padInfoDir/dump" );    

?>