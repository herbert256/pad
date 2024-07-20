<?php

  if ( count ($names) == 1 )
    if ( isset ( $padSeqStore [$name] ) )
      return $padSeqStore [$name];
    
  padFindNames ( $padSeqStore, $names );

?>