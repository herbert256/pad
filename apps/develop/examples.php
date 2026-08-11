<?php

  if ( ! isset ( $go ) )
    return;

  padDeleteDataDir ( DATA . 'examples' );

  getHarvest ( '&padExamples' );

?>
