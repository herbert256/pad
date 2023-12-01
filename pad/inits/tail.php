<?php

  if ( ! defined ( 'padTail' ) ) {

    if ( ! $padTail ) {

      define ( 'padTail',  FALSE );
      define ( 'padXml',   FALSE );
      define ( 'padXref',  FALSE );
      define ( 'padTrace', FALSE );

    } else {

      define ( 'padTail', TRUE );
      
      if ( $padXml )  define ( 'padXml',  TRUE  );
      else            define ( 'padXml',  FALSE );

      if ( $padXref ) define ( 'padXref', TRUE  );
      else            define ( 'padXref', FALSE );

      if ( $padTrace ) define ( 'padTrace', TRUE  );
      else             define ( 'padTrace', FALSE );

    }

    if ( padTail )
      include pad . 'tail/events/start.php';

  }

?>