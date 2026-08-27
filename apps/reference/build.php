<?php


  if ( isset ( $go ) ) {

    padDeleteDataDir ( DATA . 'reference'  );

    set_time_limit ( 0 );

    referenceBuild ();

    padRedirect ( 'index' );

  }

  $title = 'Build';


?>