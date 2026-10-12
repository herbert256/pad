<?php

  // A vote posted to the page of the poll, as its region's script posts it: the answer is the
  // region alone, with the results. A fresh store first, so the vote is the only one. The
  // same answer posted without the region is the form of a browser without scripting: the
  // whole page comes back, the results in it.

  @unlink ( padPollFile ( 'fw-form' ) );

  $pollUrl = $padHost . 'regression/framework/?tags/poll_shows_its_answers_as_a_form_before_a_vote&padInclude';

  // A vote counts from a session the poll was shown to: the page is asked first, and its
  // session posts the vote.

  $pollSeen  = padCurl ( $pollUrl );
  $pollLive  = padCurl ( [ 'url' => $pollUrl, 'cookies' => $pollSeen ['cookies'],
                           'post' => [ 'padLive' => 'pad-poll-fw-form', 'padEvent' => 'vote', 'padValue' => '',
                                       'padPoll' => 'fw-form', 'padPollAnswer' => 'Go' ] ] );
  $plainSeen = padCurl ( $pollUrl );
  $pollPlain = padCurl ( [ 'url' => $pollUrl, 'cookies' => $plainSeen ['cookies'], 'post' => [ 'padPoll' => 'fw-form', 'padPollAnswer' => 'PHP' ] ] );

  $vote  = $pollLive ['result'] . ': ' . pollWords ( $pollLive ['data'] );
  $again = $pollPlain ['result'] . ': ' . ( str_contains ( $pollPlain ['data'], '<style>' ) ? 'the page - ' : 'the region - ' )
         . pollWords ( $pollPlain ['data'] );

  // The words of an answer, without its style and script.

  function pollWords ( $html ) {

    $html = preg_replace ( '/<(style|script)\b.*?<\/\1>/s', '', $html );

    return trim ( preg_replace ( '/\s+/', ' ', preg_replace ( '/<[^>]+>/', ' ', $html ) ) );

  }

?>
