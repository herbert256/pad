<?php

  // Fires from level/end.php once a level has produced its final result, just before its
  // state is reset and $pad is decremented.
  //
  // Closes the level's block in the trace and xml reports, then chains to events/options.php
  // to record the tag's options in the xref.

  global $padInfoTrace, $padInfoXml;

  if ( $padInfoTrace ) include PAD . 'info/types/trace/level/end.php';
  if ( $padInfoXml   ) include PAD . 'info/types/xml/level/end.php';

  include PAD . 'events/options.php'

?>