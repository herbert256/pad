<?php

  if ( ! isset ( $go ) )
    return;

  padDeleteDataDir ( DATA . 'reference' );

  getHarvest ( '&padReference' );

?>
