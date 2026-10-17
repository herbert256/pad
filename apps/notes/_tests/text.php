<?php

  // A request value sent as a list - ?q[]=x, a posted color[]=x - is no text: the default,
  // where the cast that read it warned and every page answered 500.

  $_GET ['q']     = [ 'x' ];
  $_GET ['tag']   = 'work';
  $_GET ['color'] = [ 'sea' ];

  $shown = implode ( '|', [ notesText ( 'q' ), notesText ( 'tag' ), notesText ( 'color', 'plain' ), notesText ( 'none', 'mint' ) ] );

?>
