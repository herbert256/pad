<?php

  padXweb ( 'sequences', 'types', $padSeqSeq );

  if ( $padSeqFor !== FALSE )
    padXweb ( 'sequences', 'builds', 'for' );
  else
    padXweb ( 'sequences', 'builds', $padSeqBuild );

?>