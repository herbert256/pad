<?php

  // Listen: a sequence played as notes. Each of the first 32 terms becomes a note - the
  // term modulo 60, five octaves up from C2, the way the OEIS 'listen' button folds a term
  // into the keyboard - and the notes are written as a WAV file into the page itself
  // (seqFunWav in _lib/fun.php), so an <audio> element plays them without JavaScript.
  // Recamán's sequence, which jumps back and forth, is the famous one to listen to.
  //
  // type arrives from the query string and is used only when it names a type the gallery
  // draws.

  $choices = seqFunTypes ();

  if ( ! isset ( $type ) or ! is_string ( $type ) or ! in_array ( $type, $choices ) )
    $type = 'recaman';

  $terms = seqFunTerms ( $type, 32 );
  $oeis  = seqFunOeis ( $terms );
  $oeis  = $oeis ? sprintf ( 'A%06d', $oeis ) : '';
  $list  = implode ( ', ', $terms );
  $notes = implode ( ' ', array_map ( 'seqFunNoteName', seqFunNotes ( $terms ) ) );
  $audio = seqFunWav ( $terms );

?>
