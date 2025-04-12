<?php

  if ( ! $padSeqParm )
    $padSeqParm = $padParm;

  $padSeqDone [] = 'increment';

  return padGetRange ( $padSeqParm,  $padSeqInc );

?>