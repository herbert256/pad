<?php

  // One card: changed, moved to another column, or deleted - or a new one, without an id.
  // A plain form, the rules on its fields; the board's dialog adds cards the same way.

  $cardId = (int) padRequest ( 'id', 0 );
  $card   = $cardId ? db ( "RECORD * FROM cards WHERE id={0}", [ $cardId ] ) : NULL;

  if ( $cardId and ! $card )
    padAbort ( 404, 'There is no such card' );

  if ( padPosted ( 'remove' ) and $card ) {
    db ( "DELETE FROM cards WHERE id={0}", [ $cardId ] );
    kanbanLog ( "{$card ['title']} was deleted" );
    padFlash ( "“{$card ['title']}” is deleted." );
    padRedirect ( 'index' );
  }

  if ( padPosted ( 'edit' ) ) {

    $label  = isset ( kanbanLabels () [ padRequest ( 'label', '' ) ] ) ? padRequest ( 'label' ) : '';
    $due    = preg_match ( '/^\d{4}-\d\d-\d\d$/', (string) padRequest ( 'due', '' ) ) ? padRequest ( 'due' ) : '';
    $column = (int) padRequest ( 'column', 0 );

    if ( $card ) {
      db ( "UPDATE cards SET title='{0}', body='{1}', label='{2}', due='{3}', updated='{4}' WHERE id={5}",
           [ padRequest ( 'title' ), padRequest ( 'body', '' ), $label, $due, kanbanNow (), $cardId ] );
      if ( $column and $column != $card ['column_id'] )
        kanbanMove ( $cardId, $column );
      else
        kanbanLog ( padRequest ( 'title' ) . ' was changed', $cardId );
    } else {
      $column = $column ?: (int) db ( "FIELD id FROM columns ORDER BY position LIMIT 1" );
      $cardId = db ( "INSERT INTO cards (column_id, position, title, body, label, due, created, updated) VALUES ({0}, {1}, '{2}', '{3}', '{4}', '{5}', '{6}', '{6}')",
                     [ $column, (float) db ( "FIELD coalesce(max(position), 0) FROM cards WHERE column_id={0}", [ $column ] ) + 1,
                       padRequest ( 'title' ), padRequest ( 'body', '' ), $label, $due, kanbanNow () ] );
      kanbanLog ( padRequest ( 'title' ) . ' was added', $cardId );
    }

    padFlash ( 'Saved.' );
    padRedirect ( 'index' );

  }

  $draft      = $card ?: [ 'id' => 0, 'title' => '', 'body' => '', 'label' => '', 'due' => '', 'column_id' => 0 ];
  $columnList = array_map ( fn ( $c ) => $c + [ 'chosen' => $c ['id'] == $draft ['column_id'] ], array_values ( db ( "ARRAY id, name FROM columns ORDER BY position" ) ?: [] ) );
  $labelList  = array_map ( fn ( $key, $name ) => [ 'key' => $key, 'name' => $name, 'chosen' => $key == $draft ['label'] ], array_keys ( kanbanLabels () ), kanbanLabels () );
  $kanbanPage = $card ? $card ['title'] : 'New card';

?>
