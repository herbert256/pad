<?php

  // The script a {sound} writes, once a page, carries every recipe there is, settled by the
  // server - a note as its Hz, a run of notes as their starts and pitches - so the browser
  // renders from the very numbers padSoundWav renders from, and a data-pad-sound of any kind
  // plays. The second {sound} writes no second script.

  $soundOut  = padUnprotect ( padUnescape ( padCode ( "{sound:tick/}{sound:pop/}" ) ) );
  $soundJson = preg_match ( '/PadSound\.add\((\{.*\}), \{/s', $soundOut, $soundM ) ? json_decode ( $soundM [1], TRUE ) : [];

  $soundRecipes = substr_count ( $soundOut, '<script' ) . ' script, '
                . ( array_keys ( $soundJson ) === padSoundKinds () ? 'every sound' : 'other sounds' ) . ', '
                . 'chime B6 at ' . round ( $soundJson ['chime'] ['voices'] [1] ['notes'] [0] [1], 2 ) . ' Hz from '
                . $soundJson ['chime'] ['voices'] [1] ['start'] . ' s, '
                . 'success ' . implode ( ' ', array_map ( fn ( $n ) => $n [0] . '@' . round ( $n [1], 1 ), $soundJson ['success'] ['voices'] [0] ['notes'] ) );

?>
