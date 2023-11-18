<?php

  if ( $padXref ) 
    include pad . 'xref/end.php';
  
  list ( $padBase [$pad], $padEndBase [$pad] ) = explode ( '{end}', $padBase[$pad], 2 );

?>