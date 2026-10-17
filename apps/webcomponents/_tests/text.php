<?php

  // A request value sent as a list - size[]=x - is no text: the default, where the (string)
  // cast that read it warned "Array to string conversion" and the page answered 500.

  $_GET ['size']  = [ 'x' ];
  $_GET ['other'] = 'text';

  $shown = implode ( '|', [ wcText ( 'size' ), wcText ( 'size', 'none' ), wcText ( 'other' ) ] );

?>
