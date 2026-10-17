<?php

  // A card moved - dropped in a column, before a card or at its end, or sent along by its
  // arrow buttons. kanban.js posts JSON to ?move&padFormat=json with the session's token in
  // X-CSRF-Token and gets the board's new version and the cards per column back ($padExpose).
  // A post without padFormat goes back to the board.

  $ok = FALSE;

  if ( padRequestMethod () == 'POST' ) {

    $ok = kanbanMove ( (int) padRequest ( 'card', 0 ), (int) padRequest ( 'column', 0 ), (int) padRequest ( 'before', 0 ) );

    if ( ! padRequestHas ( 'padFormat' ) )
      padRedirect ( 'index' );

  }

  $version    = kanbanVersion ();
  $counts     = kanbanCounts ();
  $padExpose  = [ 'ok', 'version', 'counts' ];
  $kanbanPage = 'Move';

?>
