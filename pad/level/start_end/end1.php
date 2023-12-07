<?php

  if ( padXref ) 
    include pad . 'info/types/xref/items/end.php';
  
  list ( $padBase [$pad], $padEndBase [$pad] ) = explode ( '@end@', $padBase[$pad], 2 );

?>