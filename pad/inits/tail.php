<?php

  if ( ! defined ( 'padTail' ) ) {

    if ( ! $padTail ) {

      define ( 'padTail', FALSE );
      define ( 'padXml',  FALSE );
      define ( 'padXref', FALSE );

    } else {

      define ( 'padTail', TRUE );
      
      if ( $padXml )  define ( 'padXml',  TRUE  );
      else            define ( 'padXml',  FALSE );
      if ( $padXref ) define ( 'padXref', TRUE  );
      else            define ( 'padXref', FALSE );

    }

  }

  if ( padTail )
    include pad . 'tail/events/start.php';

?>