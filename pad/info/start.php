<?php
 
  include_once pad . 'info/lib.php';

  if ( $padTrack   ) include pad . 'info/types/track/start.php';
  if ( $padStats   ) include pad . 'info/types/stats/start.php';
  if ( $padXweb    ) include pad . 'info/types/reference/start.php';

  if ( padTrace    ) include pad . 'info/types/trace/start.php';
  if ( padXml      ) include pad . 'info/types/xml/start.php';
  if ( padXref     ) include pad . 'info/types/xref/start.php';
  
?>