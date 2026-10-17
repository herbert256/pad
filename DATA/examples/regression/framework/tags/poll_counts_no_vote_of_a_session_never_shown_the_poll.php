<?php

  // A vote posted without the session the poll was shown to - a loop of posts without the
  // cookie, a form on another site - is not counted: the voting post started the session
  // itself, and five posts were five votes.

  include_once PAD . 'tags/_lib/poll.php';

  @unlink ( padPollFile ( 'fw-unseen' ) );

  $pollUrl = $padHost . 'regression/framework/?tags/poll_of_a_name_of_its_own_shows_its_form&padInclude';

  for ( $i = 0; $i < 5; $i++ )
    padCurl ( [ 'url' => $pollUrl, 'post' => [ 'padPoll' => 'fw-unseen', 'padPollAnswer' => 'Go' ] ] );

  $counted = array_sum ( padPollCounts ( 'fw-unseen' ) );

?>
