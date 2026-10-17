<?php

  // A request value sent as a list - ?q[]=x, ?month[]=x - is no text and no month: the
  // default and the newest month, where the cast that read it warned and every page
  // answered 500.

  $_GET ['q']      = [ 'x' ];
  $_GET ['amount'] = '3.20';

  $shown = implode ( '|', [ ledgerText ( 'q' ), ledgerText ( 'amount' ), ledgerMonth ( [ 'x' ] ) === ledgerMonth ( '' ) ? 'newest' : 'other' ] );

?>
