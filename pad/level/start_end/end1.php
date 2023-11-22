<?php

  if ( $padXref ) 
    include pad . 'tail/types/xref/items/end.php';
  
  list ( $padBase [$pad], $padEndBase [$pad] ) = explode ( '@end@', $padBase[$pad], 2 );

?>