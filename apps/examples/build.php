<?php


  if ( isset ( $go ) ) {

    padDeleteDataDir ( DATA . 'examples'  );

    set_time_limit ( 0 );

    examplesBuild ();

    padRedirect ( 'index' );

  }

  $title = 'Build';


?>