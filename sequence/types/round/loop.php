<?php

  if ( ! $padSeqParm )
    $padSeqParm = 1;

  return round ( $padSeqLoop / $padSeqParm ) * $padSeqParm;

?>