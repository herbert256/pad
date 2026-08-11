<?php

  if ( ! isset ( $go ) )
    return;

  // The full harvest: clear both develop-owned stores and regather them from every page.
  // The regression application's fresh build asks for this page first, because the
  // reference and manual applications render from what it gathers.

  padDeleteDataDir ( DATA . 'reference' );
  padDeleteDataDir ( DATA . 'examples'  );

  set_time_limit ( 0 );

  getHarvest ( '&padExamples&padReference' );

?>
