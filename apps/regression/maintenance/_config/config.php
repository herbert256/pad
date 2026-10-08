<?php

  // The maintenance fixture: _common off; ?plain asks for the answer of an application
  // without error pages - the message as one plain line.

  $padCommon     = FALSE;
  $padErrorPages = ! isset ( $_GET ['plain'] );

?>
