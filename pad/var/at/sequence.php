<?php

  if ( $name ) 
    return padAtSearch ( $padSeqStore [$name], $names );
  else
    return padAtSearch ( $padSeqStore, $names );

?>