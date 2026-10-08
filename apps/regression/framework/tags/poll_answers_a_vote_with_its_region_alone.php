<?php

  // A vote posted to the page of the poll, as its region's script posts it: the answer is the
  // region alone, with the results. A fresh store first, so the vote is the only one. The
  // same answer posted without the region is the form of a browser without scripting: the
  // whole page comes back, the results in it.

  @unlink ( padPollFile ( 'fw-form' ) );

  $pollUrl = $padHost . 'regression/framework/?tags/poll_shows_its_answers_as_a_form_before_a_vote&padInclude';

  $pollLive  = padCurl ( [ 'url' => $pollUrl, 'post' => [ 'padLive' => 'pad-poll-fw-form', 'padEvent' => 'vote', 'padValue' => '',
                                                          'padPoll' => 'fw-form', 'padPollAnswer' => 'Go' ] ] );
  $pollPlain = padCurl ( [ 'url' => $pollUrl, 'post' => [ 'padPoll' => 'fw-form', 'padPollAnswer' => 'PHP' ] ] );

  $vote  = $pollLive ['result'] . ': ' . pollWords ( $pollLive ['data'] );
  $again = $pollPlain ['result'] . ': ' . ( str_contains ( $pollPlain ['data'], '<style>' ) ? 'the page - ' : 'the region - ' )
         . pollWords ( $pollPlain ['data'] );

  // The words of an answer, without its style and script.

  function pollWords ( $html ) {

    $html = preg_replace ( '/<(style|script)\b.*?<\/\1>/s', '', $html );

    return trim ( preg_replace ( '/\s+/', ' ', preg_replace ( '/<[^>]+>/', ' ', $html ) ) );

  }

?>
