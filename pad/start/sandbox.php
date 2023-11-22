<?php

  $padStartType = 'sandbox';

  if ( $padXref ) 
    include pad . 'tail/types/xref/items/entry.php';

  include pad . 'start/lib/start.php';
  include pad . 'start/lib/setup.php';

  $padBase [$pad] = $padTrue [$pad-1];    

  $padOccurTypeSet = 'sandbox';  
  include pad . 'occurrence/start.php'; 

  include pad . 'start/lib/level.php'; 
  include pad . 'start/lib/end.php';

  return $padPad [$pad+1];

?>