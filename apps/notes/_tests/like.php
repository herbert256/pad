<?php

  // A search for % or _ finds the notes that hold the character, where it went into LIKE
  // as a wildcard and found every note.

  $holds = fn ( $c ) => array_column ( db ( "ARRAY id FROM notes WHERE instr(title, '{0}') OR instr(body, '{0}')
                                             OR id IN (SELECT note_id FROM tags WHERE instr(tag, '{0}'))", [ $c ] ) ?: [], 'id' );
  $found = fn ( $c ) => array_column ( notesList ( $c ), 'id' );
  $same  = fn ( $a, $b ) => ( sort ( $a ) and sort ( $b ) and $a == $b );

  $shown = notesLike ( '100%_a!' ) . ' ' . ( ( $same ( $found ( '%' ), $holds ( '%' ) ) and $same ( $found ( '_' ), $holds ( '_' ) ) ) ? 'as written' : 'as wildcards' );

?>
