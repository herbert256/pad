<?php

  if ( function_exists ( 'padErrorGo' ) )
    padErrorGo ( $padExceptionError, $padExceptionFile, $padExceptionLine );
  else
    padBootStop ( $padExceptionError, $padExceptionFile, $padExceptionLine );

  padStop ( 500 );

?>