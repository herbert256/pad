<?php

  // klondike - the patience of the gold rush, with one pack of cards: seven piles are dealt,
  // the top card of each face up, and the four suits are to be built up from ace to king.
  // On the piles a card goes on one a rank higher of the other colour, a run of cards
  // moves as one, an empty pile takes a king, and the card under a moved one turns up; the
  // stock turns draw= cards at a time onto the waste, 1 unless given (1 to 3, 3 the hard
  // way), whose top card plays, and the waste turns back into the stock as often as it runs
  // out. Click a card and then where it goes - a card clicked twice goes home, or onto a
  // pile, when it can - or use Tab and Enter; Home sends up every card that can go, Undo
  // takes moves back. The same deal for the same seed=.

  return padGame ( 'klondike', 'Klondike', [
    'draw' => padGameInt ( 'draw', 1, 1, 3 )
  ] );

?>
