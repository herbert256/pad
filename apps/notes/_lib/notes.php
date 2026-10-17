<?php

  // The notes, their tags and attachments - the queries every page of the application shares.

  // The colours a note can have, by name, with what a screen reader says.

  function notesColors () {

    return [ 'plain' => 'Plain', 'sun' => 'Sun', 'sea' => 'Sea', 'mint' => 'Mint', 'rose' => 'Rose', 'lilac' => 'Lilac' ];

  }

  // A request value that is one text - a search, a tag, a colour, the Markdown to preview:
  // its text, or the default for a list. ?q[]=x sends one, and the (string) cast that read it warned "Array
  // to string conversion" - an error under PAD - so every page answered 500; a posted
  // color[]=x, used as a key, threw.

  function notesText ( $name, $default = '' ) {

    $value = padRequest ( $name, $default );

    return is_scalar ( $value ) ? (string) $value : $default;

  }

  function notesNow () {

    return date ( 'Y-m-d H:i:s' );

  }

  // Pinned notes first, then the last changed: all of them, those of one tag, or those a
  // search finds in the title, the text or a tag. Each with its tags, an excerpt of its text
  // without the Markdown, and how many files it has.

  function notesList ( $search = '', $tag = '' ) {

    $where = [];
    $vars  = [];

    if ( $search !== '' ) {
      $where [] = "(title LIKE '%{0}%' ESCAPE '!' OR body LIKE '%{0}%' ESCAPE '!' OR id IN (SELECT note_id FROM tags WHERE tag LIKE '%{0}%' ESCAPE '!'))";
      $vars  [0] = notesLike ( $search );
    }

    if ( $tag !== '' ) {
      $where [] = "id IN (SELECT note_id FROM tags WHERE tag = '{1}')";
      $vars  [1] = $tag;
    }

    $notes = db ( "ARRAY id, title, body, color, pinned, created, updated,
                     (SELECT count(*) FROM attachments a WHERE a.note_id = notes.id) AS files,
                     (SELECT group_concat(tag, ',') FROM (SELECT tag FROM tags t WHERE t.note_id = notes.id ORDER BY tag)) AS tagline
                   FROM notes " . ( $where ? 'WHERE ' . implode ( ' AND ', $where ) : '' ) . "
                   ORDER BY pinned DESC, updated DESC, id DESC", $vars ) ?: [];

    foreach ( $notes as &$note ) {
      $note ['excerpt'] = notesExcerpt ( $note ['body'] );
      $note ['tags']    = notesTagRows ( $note ['tagline'] );
      $note ['day']     = notesDay ( $note ['updated'] );
      unset ( $note ['body'], $note ['tagline'] );
    }

    return $notes;

  }

  // A search word for LIKE, as it is written: % and _ are LIKE's wildcards, so a search for
  // either found every note, and 100% every note with 100 in it. They are escaped with the
  // ! that the queries name in ESCAPE '!'.

  function notesLike ( $word ) {

    return str_replace ( [ '!', '%', '_' ], [ '!!', '!%', '!_' ], (string) $word );

  }

  function notesTagRows ( $line ) {

    return array_map ( fn ( $tag ) => [ 'tag' => $tag ], array_filter ( explode ( ',', (string) $line ), 'strlen' ) );

  }

  // Every tag with the number of notes that have it.

  function notesTags () {

    return db ( "ARRAY tag, count(*) AS uses FROM tags GROUP BY tag ORDER BY tag" ) ?: [];

  }

  function notesNote ( $id ) {

    $note = db ( "RECORD * FROM notes WHERE id={0}", [ (int) $id ] );

    if ( ! $note )
      return NULL;

    $note ['tags']  = db ( "ARRAY tag FROM tags WHERE note_id={0} ORDER BY tag", [ $note ['id'] ] ) ?: [];
    $note ['files'] = notesFiles ( $note ['id'] );
    $note ['day']   = notesDay ( $note ['updated'] );
    $note ['born']  = notesDay ( $note ['created'] );

    return $note;

  }

  function notesFiles ( $id ) {

    $files = db ( "ARRAY * FROM attachments WHERE note_id={0} ORDER BY id", [ (int) $id ] ) ?: [];

    foreach ( $files as &$file ) {
      $file ['picture'] = str_starts_with ( $file ['type'], 'image/' ) && $file ['type'] != 'image/svg+xml';
      $file ['bytes']   = notesSize ( $file ['size'] );
    }

    return $files;

  }

  // The text without its Markdown, cut at a word near 160 characters.

  function notesExcerpt ( $body ) {

    $text = preg_replace ( [ '/^#+\s*/m', '/^>\s?/m', '/^\s*([-*+]|\d+\.)\s+/m', '/[*_`]+/', '/\[([^\]]*)\]\([^)]*\)/', '/^-{3,}$/m' ], [ '', '', '', '', '$1', '' ], (string) $body );
    $text = trim ( preg_replace ( '/\s+/', ' ', $text ) );

    if ( mb_strlen ( $text ) <= 160 )
      return $text;

    $cut = mb_substr ( $text, 0, 160 );

    return rtrim ( mb_substr ( $cut, 0, mb_strrpos ( $cut, ' ' ) ?: 160 ), ' ,.;:-' ) . '…';

  }

  function notesDay ( $stamp ) {

    $time = strtotime ( (string) $stamp );

    return $time ? date ( 'j M Y', $time ) : '';

  }

  function notesSize ( $bytes ) {

    return padNumberFileSize ( (int) $bytes );

  }

  function notesPin ( $id ) {

    db ( "UPDATE notes SET pinned = 1 - pinned WHERE id={0}", [ (int) $id ] );

  }

  // A note's tags as one line typed by hand - 'work, ideas' - lower case, without doubles.

  function notesSaveTags ( $id, $line ) {

    db ( "DELETE FROM tags WHERE note_id={0}", [ (int) $id ] );

    $tags = array_unique ( array_filter ( array_map ( fn ( $t ) => mb_substr ( trim ( mb_strtolower ( $t ) ), 0, 40 ), preg_split ( '/[,;]+/', (string) $line ) ), 'strlen' ) );

    foreach ( $tags as $tag )
      db ( "INSERT INTO tags (note_id, tag) VALUES ({0}, '{1}')", [ (int) $id, $tag ] );

  }

  // A note gone with its files - the rows go with the note (ON DELETE CASCADE), the stored
  // files are removed here.

  function notesDelete ( $id ) {

    foreach ( notesFiles ( $id ) as $file )
      @unlink ( DATA . $file ['file'] );

    db ( "DELETE FROM notes WHERE id={0}", [ (int) $id ] );

  }

  function notesDetach ( $attachment ) {

    $file = db ( "RECORD * FROM attachments WHERE id={0}", [ (int) $attachment ] );

    if ( ! $file )
      return 0;

    @unlink ( DATA . $file ['file'] );

    db ( "DELETE FROM attachments WHERE id={0}", [ $file ['id'] ] );
    db ( "UPDATE notes SET updated='{0}' WHERE id={1}", [ notesNow (), $file ['note_id'] ] );

    return (int) $file ['note_id'];

  }

  // The types a note may carry: pictures, PDFs, text.

  function notesTypes () {

    return [ 'image/png', 'image/jpeg', 'image/gif', 'image/webp', 'application/pdf', 'text/plain', 'text/markdown', 'text/csv' ];

  }

?>
