<?php

  // A request value sent as a list - op[]=x - is no text: the default, where the (string)
  // cast that read it warned "Array to string conversion" and the page answered 500.

  $_GET ['op']    = [ 'x' ];
  $_GET ['other'] = 'text';

  $shown = implode ( '|', [ islandsText ( 'op' ), islandsText ( 'op', 'none' ), islandsText ( 'other' ) ] );

?>
