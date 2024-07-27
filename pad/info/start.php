<?php
 
  include_once pad . 'info/_lib.php';

  if ( $padTrack ) include pad . 'info/types/track/start.php';
  if ( $padStats ) include pad . 'info/types/stats/start.php';
  
  if ( padXapp   ) include pad . 'info/types/xapp/start.php';
  if ( padTrace  ) include pad . 'info/types/trace/start.php';
  if ( padXml    ) include pad . 'info/types/xml/start.php';
  if ( padXref   ) include pad . 'info/types/xref/start.php';
  
?>