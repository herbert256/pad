<?php

  // A request value sent as a list - a posted label[]=x or due[]=x - is no text: the
  // default, where it threw as a key or warned as a cast and the post answered 500.

  $_GET ['label'] = [ 'bug' ];
  $_GET ['due']   = '2026-10-17';

  $shown = implode ( '|', [ kanbanText ( 'label' ), kanbanText ( 'due' ), kanbanText ( 'label', 'idea' ) ] );

?>
