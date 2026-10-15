<?php

  // The board: its columns with their cards, the moves, the activity and the version every
  // open window watches.

  function kanbanLabels () {

    return [ 'feature' => 'Feature', 'bug' => 'Bug', 'idea' => 'Idea', 'chore' => 'Chore' ];

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
  // between its new neighbours, so no other card moves. FALSE when the card or the column
  // is not there.

  function kanbanMove ( $cardId, $columnId, $beforeId = 0 ) {

    $card   = db ( "RECORD * FROM cards WHERE id={0}", [ (int) $cardId ] );
    $column = db ( "RECORD * FROM columns WHERE id={0}", [ (int) $columnId ] );

    if ( ! $card or ! $column )
      return FALSE;

    $before = $beforeId ? db ( "RECORD * FROM cards WHERE id={0} AND column_id={1} AND id != {2}", [ (int) $beforeId, $column ['id'], $card ['id'] ] ) : NULL;

    if ( $before ) {
      $previous = db ( "FIELD max(position) FROM cards WHERE column_id={0} AND position < {1} AND id != {2}", [ $column ['id'], $before ['position'], $card ['id'] ] );
      $position = $previous === NULL || $previous === '' ? $before ['position'] - 1 : ( $previous + $before ['position'] ) / 2;
    } else
      $position = (float) db ( "FIELD coalesce(max(position), 0) FROM cards WHERE column_id={0} AND id != {1}", [ $column ['id'], $card ['id'] ] ) + 1;

    db ( "UPDATE cards SET column_id={0}, position={1}, updated='{2}' WHERE id={3}", [ $column ['id'], $position, kanbanNow (), $card ['id'] ] );

    kanbanLog ( $card ['column_id'] == $column ['id'] ? "{$card ['title']} moved within {$column ['name']}" : "{$card ['title']} went to {$column ['name']}", $card ['id'] );

    return TRUE;

  }

  // What the event stream sends when the board moved on: the version, the number of cards
  // per column, the newest activity.

  function kanbanSummary () {

    return [ 'version' => kanbanVersion (), 'counts' => kanbanCounts (), 'latest' => kanbanActivity ( 1 ) [0] ['what'] ?? '' ];

  }

?>
