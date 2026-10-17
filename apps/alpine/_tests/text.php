<?php

  // A request value sent as a list - q[]=x - is no text: the default, where the (string)
  // cast that read it warned "Array to string conversion" and the page answered 500.

  $_GET ['q']     = [ 'x' ];
  $_GET ['other'] = 'text';

  $shown = implode ( '|', [ alpineText ( 'q' ), alpineText ( 'q', 'none' ), alpineText ( 'other' ) ] );

?>
