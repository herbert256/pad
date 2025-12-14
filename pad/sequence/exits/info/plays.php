<?php

  foreach ( $pqPlays as $pqTmp ) {

    extract ( $pqTmp );

    $pqInfo ['plays'] [] = "$pqPlay/$pqSeq";

  }

?>