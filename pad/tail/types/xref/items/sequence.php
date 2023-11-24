<?php

  padXref ( 'sequences', 'types', $padSeqSeq );

  if ( $padSeqFor !== FALSE )
    padXref ( 'sequences', 'builds', 'for' );
  else
    padXref ( 'sequences', 'builds', $padSeqBuild );

?>