<?php

  function links ( $dir = '' ) {
    
    if ( ! $dir )
      return [];

    foreach ( glob ( "$dir*.pad" ) as $file ) {

      $link = str_replace ( $dir,   '', $file   );
      $link = str_replace ( '.pad', '', $link );

      $links [] ['link'] = $link;

    }

  }


?>