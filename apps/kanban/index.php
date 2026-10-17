<?php

  // The board. Its columns are a fragment - {fragment 'columns'} - that kanban.js asks for
  // again when the board moved on, and so is the activity: ?index&padFragment=columns. A new
  // card comes from the dialog's form, posted here.

  if ( padPosted ( 'card' ) ) {

    $column = (int) db ( "FIELD id FROM columns ORDER BY position LIMIT 1" );
    $label  = kanbanText ( 'label' );
    $label  = isset ( kanbanLabels () [$label] ) ? $label : '';
    $id     = db ( "INSERT INTO cards (column_id, position, title, body, label, due, created, updated) VALUES ({0}, {1}, '{2}', '{3}', '{4}', '', '{5}', '{5}')",
                   [ $column, (float) db ( "FIELD coalesce(max(position), 0) FROM cards WHERE column_id={0}", [ $column ] ) + 1,
                     padRequest ( 'title' ), padRequest ( 'body', '' ), $label, kanbanNow () ] );

    kanbanLog ( padRequest ( 'title' ) . ' was added', $id );
    padRedirect ( 'index' );

  }

  $board    = kanbanBoard ();
  $activity = kanbanActivity ();
  $version  = kanbanVersion ();
  $cardTotal = array_sum ( array_column ( $board, 'count' ) );
  // A card that came back - no title, or one too long - opens the dialog again, with its
  // message and the label that was chosen: the message stood in the closed dialog, and the
  // board reloaded without a word.

  $cardFailed = padFormFailed ( 'card' );
  $cardLabel  = $cardFailed ? kanbanText ( 'label' ) : '';
  $labelList  = array_map ( fn ( $key, $name ) => [ 'key' => $key, 'name' => $name, 'chosen' => $key === $cardLabel ], array_keys ( kanbanLabels () ), kanbanLabels () );

?>
