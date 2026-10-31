<?php

  // A poll - the poll widget: a question with its answers as a form, and after a vote the
  // results as bars with their percentages.
  //
  //   {poll 'favorite-language', options='PHP, Python, Ruby, Go', title='Your favourite language?'}
  //
  // The form stands in a {live} region (lib/live.php): with scripting on, the vote is posted
  // in the background and the region swaps in the results; with scripting off the form posts
  // to the page itself, the way forms always have, and the page comes back with the results.
  // Either way it is a POST of the page's own URL, so $padCsrf protects it as it protects
  // every form - the field is added to the form, the region carries the token for the script.
  //
  // A visitor votes once per poll: the answer is kept in the session, and a visitor who
  // voted sees the results from then on. The votes are kept in DATA/poll/<app>/<name>.json,
  // counted per answer under an exclusive lock (flock), so two votes at the same moment are
  // both counted; an answer taken out of options= keeps its count in the file, and shows no
  // more. results shows the results to everyone, also before a vote - a results page.
  //
  // The name is letters, digits, _ and -: it names the file and the region.
  //
  // padPollVoted   the answer this visitor gave, '' before a vote - read without starting a
  //                session for a visitor who has none
  // padPollPosted  the vote this request posts for the poll: the answer, '' when none
  // padPoll        the widget: the form, or the results, in its region
  // padPollStyle   the CSS, assets/poll.css, once per page
  //
  // The votes themselves - padPollFile, padPollCounts and padPollVote - are kept by
  // lib/poll.php, for a page's PHP as well as for the widget.

  function padPollVoted ( $name ) {

    if ( ! padSessionExists () or ! padSessionStart () )
      return '';

    $answer = $_SESSION ['padPoll'] [ $GLOBALS ['padApp'] ] [$name] ?? '';

    return is_string ( $answer ) ? $answer : '';

  }

  function padPollPosted ( $name ) {

    if ( ( $_SERVER ['REQUEST_METHOD'] ?? '' ) != 'POST' or ( $_POST ['padPoll'] ?? '' ) !== $name )
      return FALSE;

    $answer = $_POST ['padPollAnswer'] ?? '';

    return is_string ( $answer ) ? $answer : '';

  }

  function padPoll ( $name, $answers, $title, $button, $results ) {

    $voted = padPollVoted ( $name );
    $note  = '';

    // A vote: an answer of the poll, once per visitor, counted only from a session that was
    // shown the poll before - the form marks it - where the voting post started the session
    // itself: a post without the cookie, a loop or a form on another site, voted again and
    // again, one request a vote. A session that is there but was not shown the poll - it
    // ended between reading and voting - is asked to vote again on the form that marks it,
    // where it was told the site keeps no session for it.

    $posted = padPollPosted ( $name );
    $app    = $GLOBALS ['padApp'];

    if ( $posted !== FALSE and $voted === '' ) {

      if ( ! in_array ( $posted, $answers, TRUE ) )
        $note = 'Choose one of the answers.';
      elseif ( ! padSessionExists () or ! padSessionStart () )
        $note = 'Your vote could not be counted - this site keeps no session for you.';
      elseif ( empty ( $_SESSION ['padPollShown'] [$app] [$name] ) )
        $note = 'Your vote could not be counted - please vote again.';
      else {
        padPollVote ( $name, $posted );
        $_SESSION ['padPoll'] [ $GLOBALS ['padApp'] ] [$name] = $posted;
        $voted = $posted;
      }

    }

    $id    = "pad-poll-$name";
    $title = padWidgetAttr ( $title );

    if ( $voted !== '' or $results )
      $inner = padPollResults ( $id, $name, $answers, $title, $voted );
    else {
      if ( padSessionStart () )
        $_SESSION ['padPollShown'] [$app] [$name] = TRUE;
      $inner = padPollForm ( $id, $name, $answers, $title, padWidgetAttr ( $button ), $note );
    }

    return padPollStyle () . padLiveWrap ( $id, padProtect ( $inner ) );

  }

  function padPollForm ( $id, $name, $answers, $title, $button, $note ) {

    $out = "<form class=\"pad-poll\" method=\"post\" pad-submit=\"vote\">"
         . "<input type=\"hidden\" name=\"padPoll\" value=\"$name\">"
         . "<fieldset class=\"pad-poll-answers\"><legend class=\"pad-poll-title\" id=\"$id-title\">$title</legend>"
         . ( $note !== '' ? '<p class="pad-poll-note" role="alert">' . padWidgetAttr ( $note ) . '</p>' : '' );

    foreach ( $answers as $index => $answer )
      $out .= "<label class=\"pad-poll-answer\"><input type=\"radio\" name=\"padPollAnswer\" value=\""
            . padWidgetAttr ( $answer ) . '"' . ( $index ? '' : ' required' ) . '> <span>' . padWidgetAttr ( $answer ) . '</span></label>';

    return $out . "</fieldset><button type=\"submit\" class=\"pad-poll-vote\">$button</button></form>";

  }

  function padPollResults ( $id, $name, $answers, $title, $voted ) {

    $counts = padPollCounts ( $name );
    $total  = 0;

    foreach ( $answers as $answer )
      $total += (int) ( $counts [$answer] ?? 0 );

    $out = "<div class=\"pad-poll pad-poll-done\" aria-labelledby=\"$id-title\" role=\"group\">"
         . "<p class=\"pad-poll-title\" id=\"$id-title\">$title</p><ul class=\"pad-poll-results\">";

    foreach ( $answers as $answer ) {

      $votes = (int) ( $counts [$answer] ?? 0 );
      $share = $total ? (int) round ( 100 * $votes / $total ) : 0;
      $mine  = ( $answer === $voted );

      $out .= '<li class="pad-poll-result' . ( $mine ? ' is-mine' : '' ) . '">'
            . '<span class="pad-poll-answer-name">' . padWidgetAttr ( $answer )
            . ( $mine ? ' <span class="pad-poll-mine">your vote</span>' : '' ) . '</span>'
            . "<span class=\"pad-poll-share\">$share%<span class=\"pad-poll-count\"> · $votes " . ( $votes == 1 ? 'vote' : 'votes' ) . '</span></span>'
            . "<span class=\"pad-poll-bar\" aria-hidden=\"true\"><span class=\"pad-poll-fill\" style=\"width:$share%\"></span></span>"
            . '</li>';

    }

    return $out . '</ul><p class="pad-poll-total">' . $total . ' ' . ( $total == 1 ? 'vote' : 'votes' ) . '</p></div>';

  }

  function padPollStyle () {

    return padWidgetStyle ( 'poll',
      [ 'accent'  => [ '#2a78d6', '#5598e7' ],
        'on'      => [ '#ffffff', '#0d1b2e' ],
        'text'    => [ '#1f1f1d', '#ecebe6' ],
        'muted'   => [ '#62615c', '#a9a8a0' ],
        'line'    => [ '#e4e3df', '#3a3a37' ],
        'track'   => [ '#efeeea', '#2a2a28' ],
        'surface' => [ '#ffffff', '#1f1f1d' ] ],
      padWidgetAsset ( 'poll.css' ) );

  }

?>
