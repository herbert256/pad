<?php

  // A vote from a session that is there but was never shown the form - it ended between
  // reading and voting, or the form came from a kept section - is not counted, and the
  // visitor is asked to vote again on the form, which now marks the session: the note said
  // the site keeps no session for them.

  include_once PAD . 'tags/_lib/poll.php';

  @unlink ( padPollFile ( 'fw-again' ) );

  $methodWas = $_SERVER ['REQUEST_METHOD'] ?? 'GET';
  $postWas   = $_POST;

  $_SERVER ['REQUEST_METHOD'] = 'POST';
  $_POST = [ 'padPoll' => 'fw-again', 'padPollAnswer' => 'Yes' ];

  padSessionStart ();
  unset ( $_SESSION ['padPollShown'] [$padApp] ['fw-again'], $_SESSION ['padPoll'] [$padApp] ['fw-again'] );

  $pollOut = padUnprotect ( padPoll ( 'fw-again', [ 'Yes', 'No' ], 'Again?', 'Vote', FALSE ) );

  $_SERVER ['REQUEST_METHOD'] = $methodWas;
  $_POST = $postWas;

  $pollNote   = preg_match ( '#<p class="pad-poll-note" role="alert">(.*?)</p>#', $pollOut, $m ) ? $m [1] : 'no note';
  $pollMarked = empty ( $_SESSION ['padPollShown'] [$padApp] ['fw-again'] ) ? 'not marked' : 'marked';
  $pollVotes  = array_sum ( padPollCounts ( 'fw-again' ) );

?>
