<?php

  if ( ! defined ( 'padInfo' ) ) {

    if ( ! $padInfo ) {

      define ( 'padInfo',  FALSE );
      define ( 'padXml',   FALSE );
      define ( 'padXref',  FALSE );
      define ( 'padTrace', FALSE );

    } else {

      define ( 'padInfo', TRUE );
      
      if ( $padXml )  define ( 'padXml',  TRUE  );
      else            define ( 'padXml',  FALSE );

      if ( $padXref ) define ( 'padXref', TRUE  );
      else            define ( 'padXref', FALSE );

      if ( $padTrace ) define ( 'padTrace', TRUE  );
      else             define ( 'padTrace', FALSE );

    }

    if ( padInfo )
      include pad . 'info/events/start.php';

  }

?>