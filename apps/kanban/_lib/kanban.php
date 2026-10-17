<?php

  // The board: its columns with their cards, the moves, the activity and the version every
  // open window watches.

  function kanbanLabels () {

    return [ 'feature' => 'Feature', 'bug' => 'Bug', 'idea' => 'Idea', 'chore' => 'Chore' ];

  }

  // A request value that is one text - a label, a day: its text, or the default for a list.
  // A posted label[]=x, used as a key, threw, and due[]=x warned "Array to string
  // conversion" - an error under PAD - so the post answered 500.

  function kanbanText ( $name, $default = '' ) {

    $value = padRequest ( $name, $default );

    return is_scalar ( $value ) ? (string) $value : $default;

  }

  function kanbanNow () {

    return date ( 'Y-m-d H:i:s' );

  }

  // The board's version: the newest activity. Every change writes one, so a window that saw
  // this number has seen the board as it is.

  function kanbanVersion () {

    return (int) db ( "FIELD coalesce(max(id), 0) FROM activity" );

  }

  function kanbanLog ( $what, $card = 0 ) {

    db ( "INSERT INTO activity (at, what, card_id) VALUES ('{0}', '{1}', {2})", [ kanbanNow (), $what, (int) $card ] );

  }

  // The columns in their order, each with its cards in theirs - and for every card the
  // columns left and right of its own, which its arrow buttons move it to.

  function kanbanBoard () {

    $columns = array_values ( db ( "ARRAY * FROM columns ORDER BY position" ) ?: [] );
    $cards   = array_values ( db ( "ARRAY * FROM cards ORDER BY position, id" ) ?: [] );

    foreach ( $columns as $n => &$column ) {

      $column ['cards'] = [];
      $left  = $columns [$n - 1] ?? NULL;
      $right = $columns [$n + 1] ?? NULL;

      foreach ( $cards as $card )
        if ( $card ['column_id'] == $column ['id'] )
          $column ['cards'] [] = $card + [
            'excerpt'   => kanbanExcerpt ( $card ['body'] ),
            'dueShown'  => $card ['due'] ? date ( 'j M', strtotime ( $card ['due'] ) ) : '',
            'labelName' => kanbanLabels () [ $card ['label'] ] ?? '',
            'left'      => $left  ['id'] ?? 0, 'leftName'  => $left  ['name'] ?? '',
            'right'     => $right ['id'] ?? 0, 'rightName' => $right ['name'] ?? ''
          ];

      $column ['count'] = count ( $column ['cards'] );
      $column ['over']  = $column ['wip'] > 0 && $column ['count'] > $column ['wip'];

    }

    return $columns;

  }

  function kanbanCounts () {

    return array_column ( db ( "ARRAY c.id, (SELECT count(*) FROM cards WHERE column_id = c.id) AS cards FROM columns c ORDER BY c.position" ) ?: [], 'cards', 'id' );

  }

  function kanbanActivity ( $count = 12 ) {

    $rows = array_values ( db ( "ARRAY id, at, what FROM activity ORDER BY id DESC LIMIT {0}", [ (int) $count ] ) ?: [] );

    foreach ( $rows as &$row )
      $row ['when'] = date ( 'j M H:i', strtotime ( $row ['at'] ) );

    return $rows;

  }

  function kanbanExcerpt ( $body ) {

    $text = trim ( preg_replace ( '/\s+/', ' ', str_replace ( [ '`', '*', '_' ], '', (string) $body ) ) );

    return mb_strlen ( $text ) > 90 ? rtrim ( mb_substr ( $text, 0, mb_strrpos ( mb_substr ( $text, 0, 90 ), ' ' ) ?: 90 ), ' ,.;:-' ) . '…' : $text;

  }

  // A card to a column: before the card named, or at its end. Its position is halfway
  // between its new neighbours, so no other card moves - until there is no room between
  // them: then the column is numbered 1, 2, 3 ... again first. FALSE when the card or the
  // column is not there.

  function kanbanMove ( $cardId, $columnId, $beforeId = 0 ) {

    $card   = db ( "RECORD * FROM cards WHERE id={0}", [ (int) $cardId ] );
    $column = db ( "RECORD * FROM columns WHERE id={0}", [ (int) $columnId ] );

    if ( ! $card or ! $column )
      return FALSE;

    $before = $beforeId ? db ( "RECORD * FROM cards WHERE id={0} AND column_id={1} AND id != {2}", [ (int) $beforeId, $column ['id'], $card ['id'] ] ) : NULL;

    if ( $before and ( $position = kanbanBetween ( $card, $column, $before ) ) === NULL ) {
      kanbanRenumber ( $column ['id'] );
      $before   = db ( "RECORD * FROM cards WHERE id={0}", [ $before ['id'] ] );
      $position = kanbanBetween ( $card, $column, $before );
    }

    if ( ! $before )
      $position = (float) db ( "FIELD coalesce(max(position), 0) FROM cards WHERE column_id={0} AND id != {1}", [ $column ['id'], $card ['id'] ] ) + 1;

    db ( "UPDATE cards SET column_id={0}, position={1}, updated='{2}' WHERE id={3}", [ $column ['id'], $position, kanbanNow (), $card ['id'] ] );

    kanbanLog ( $card ['column_id'] == $column ['id'] ? "{$card ['title']} moved within {$column ['name']}" : "{$card ['title']} went to {$column ['name']}", $card ['id'] );

    return TRUE;

  }

  // The position halfway between the card a card is dropped before and the one above it,
  // or one less than the first's - NULL when there is no room. A position is written with
  // the 14 digits db() gives a number, so after some 45 drops into the same slot the half
  // was equal to a neighbour once written: the card above got the position of the one
  // below, and the next drop there, finding nothing above, sent the card to the top. A
  // card sharing the position of the one dropped before is no room either - which of the
  // two shows first is a matter of their ids.

  function kanbanBetween ( $card, $column, $before ) {

    if ( db ( "CHECK cards WHERE column_id={0} AND position = {1} AND id != {2} AND id != {3}", [ $column ['id'], $before ['position'], $before ['id'], $card ['id'] ] ) )
      return NULL;

    $previous = db ( "FIELD max(position) FROM cards WHERE column_id={0} AND position < {1} AND id != {2}", [ $column ['id'], $before ['position'], $card ['id'] ] );

    if ( $previous === NULL or $previous === '' )
      return $before ['position'] - 1;

    $position = ( $previous + $before ['position'] ) / 2;
    $written  = (float) (string) $position;

    return ( $written > $previous and $written < $before ['position'] ) ? $position : NULL;

  }

  // A column's cards numbered 1, 2, 3 ... in the order the board shows them.

  function kanbanRenumber ( $columnId ) {

    $ids = array_column ( db ( "ARRAY id FROM cards WHERE column_id={0} ORDER BY position, id", [ (int) $columnId ] ) ?: [], 'id' );

    foreach ( array_values ( $ids ) as $n => $id )
      db ( "UPDATE cards SET position={0} WHERE id={1}", [ $n + 1, (int) $id ] );

  }

  // What the event stream sends when the board moved on: the version, the number of cards
  // per column, the newest activity.

  function kanbanSummary () {

    return [ 'version' => kanbanVersion (), 'counts' => kanbanCounts (), 'latest' => kanbanActivity ( 1 ) [0] ['what'] ?? '' ];

  }

?>
