<?php

  if ( count ( $pqActionList ) and file_exists ( "sequence/actions/merge/$pqAction" ) )
    foreach ( $pqActionList as $pqMerge ) 
      $pqResult = array_merge ( $pqResult, $pqStore [$pqMerge] );
  
?>