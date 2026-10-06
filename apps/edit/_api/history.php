<?php

  // The earlier versions of a file: the list, or the text of one.

  [ $app, $root, $rel ] = editTarget ( $body );

  return match ( editArg ( $body, 'op', 'list' ) ) {
    'list'  => editHistoryList ( editStore (), $app, $root, $rel ),
    'get'   => [ 'text' => editHistoryGet ( editStore (), $app, $root, $rel, editArg ( $body, 'id' ) ) ],
    default => editFail ( 'history: list or get' )
  };

?>
