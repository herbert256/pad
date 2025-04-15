<?php

  if ( $padSeqPullName and isset ( $padSeqStore [$padSeqPullName] ) )
    return 'pull';

  return FALSE;
    
?>