<?php

  padXapp ( 'sequences', 'types', $padSeqSeq );

  if ( $padSeqFor !== FALSE )
    padXapp ( 'sequences', 'builds', 'for' );
  else
    padXapp ( 'sequences', 'builds', $padSeqBuild );

?>