<?php

  if ( ! $padSeqNand )
    $padSeqNand = 1;

  return ~ ( $padSeqLoop & (int) $padSeqNand );

?>