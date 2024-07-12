<?php

  if ( padXref )  include pad . 'info/types/xref/events/end.php';
  if ( padXapp )  include pad . 'info/types/xapp/events/end.php';
  
  list ( $padBase [$pad], $padEndBase [$pad] ) = explode ( '@end@', $padBase[$pad], 2 );

?>