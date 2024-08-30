<?php

  if ( ! $padSeqParm or $padSeqParm === TRUE or ! str_contains ( $padSeqParm, '..' ) )
    $padSeqParm = '1..25';

  return padGetRange ( $padSeqParm,  $padSeqInc );

?>