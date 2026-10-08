<?php

  // A poll - the {poll} tag: a question with its answers as a form, and after a vote the
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
  // padPollFile    the file of a poll's votes
  // padPollCounts  the votes per answer, read under a shared lock
  // padPollVote    one vote added, under an exclusive lock - also for a page's PHP or a test
  // padPollVoted   the answer this visitor gave, '' before a vote - read without starting a
  //                session for a visitor who has none
  // padPollPosted  the vote this request posts for the poll: the answer, '' when none
  // padPoll        the widget: the form, or the results, in its region
  // padPollStyle   the CSS, once per page

  function padPollFile ( $name ) {

    return DATA . 'poll/' . $GLOBALS ['padApp'] . "/$name.json";

  }

  function padPollCounts ( $name ) {

    $file = padPollFile ( $name );

    if ( ! is_file ( $file ) or ! ( $handle = fopen ( $file, 'r' ) ) )
      return [];

    flock ( $handle, LOCK_SH );
    $json = stream_get_contents ( $handle );
    flock ( $handle, LOCK_UN );
    fclose ( $handle );

    $counts = json_decode ( (string) $json, TRUE );

    return is_array ( $counts ) ? $counts : [];

  }

  function padPollVote ( $name, $answer ) {

    $file = padPollFile ( $name );

    if ( ! is_dir ( dirname ( $file ) ) )
      @mkdir ( dirname ( $file ), 0775, TRUE );

    if ( ! ( $handle = @fopen ( $file, 'c+' ) ) )
      return padError ( "the votes of the poll '$name' cannot be written" );

    flock ( $handle, LOCK_EX );

    $counts = json_decode ( (string) stream_get_contents ( $handle ), TRUE );

    if ( ! is_array ( $counts ) )
      $counts = [];

    $counts [$answer] = ( (int) ( $counts [$answer] ?? 0 ) ) + 1;

    ftruncate ( $handle, 0 );
    rewind    ( $handle );
    fwrite    ( $handle, json_encode ( $counts, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT ) );
    fflush    ( $handle );
    flock     ( $handle, LOCK_UN );
    fclose    ( $handle );

    return $counts;

  }

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

    // A vote: an answer of the poll, once per visitor, counted only with a session to
    // remember it in.

    $posted = padPollPosted ( $name );

    if ( $posted !== FALSE and $voted === '' ) {

      if ( ! in_array ( $posted, $answers, TRUE ) )
        $note = 'Choose one of the answers.';
      elseif ( ! padSessionStart () )
        $note = 'Your vote could not be counted - this site keeps no session for you.';
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
    else
      $inner = padPollForm ( $id, $name, $answers, $title, padWidgetAttr ( $button ), $note );

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
      '.pad-poll{max-width:28rem;margin:0 0 1em;padding:1.1em 1.2em;border:1px solid var(--pad-poll-line);border-radius:12px;'
      .   'background:var(--pad-poll-surface);color:var(--pad-poll-text)}'
      . '.pad-poll-title{margin:0 0 .7em;padding:0;font-weight:700;font-size:1.05em}'
      . '.pad-poll-note{margin:0;color:var(--pad-poll-accent);font-weight:600}'
      . '.pad-poll-answers{display:grid;gap:.4em;margin:0 0 .9em;padding:0;border:0}'
      . '.pad-poll-answer{display:flex;align-items:center;gap:.6em;padding:.5em .7em;border:1px solid var(--pad-poll-line);'
      .   'border-radius:8px;cursor:pointer}'
      . '.pad-poll-answer:hover{border-color:var(--pad-poll-muted)}'
      . '.pad-poll-answer:has(:checked){border-color:var(--pad-poll-accent);box-shadow:inset 0 0 0 1px var(--pad-poll-accent)}'
      . '.pad-poll-answer input{accent-color:var(--pad-poll-accent);margin:0}'
      . '.pad-poll-vote{padding:.55em 1.3em;border:0;border-radius:8px;background:var(--pad-poll-accent);'
      .   'color:var(--pad-poll-on);font:inherit;font-weight:600;cursor:pointer}'
      . '.pad-poll-vote:focus-visible,.pad-poll-answer:has(:focus-visible){outline:2px solid var(--pad-poll-accent);outline-offset:2px}'
      . '[data-pad-live][aria-busy] .pad-poll{opacity:.6}'
      . '.pad-poll-results{display:grid;gap:.75em;margin:0;padding:0;list-style:none}'
      . '.pad-poll-result{display:grid;grid-template-columns:1fr auto;gap:.3em .8em}'
      . '.pad-poll-share{font-variant-numeric:tabular-nums;font-weight:600}'
      . '.pad-poll-count{color:var(--pad-poll-muted);font-weight:400}'
      . '.pad-poll-mine{margin-left:.4em;padding:.05em .5em;border-radius:99px;background:var(--pad-poll-accent);'
      .   'color:var(--pad-poll-on);font-size:.75em;font-weight:600;vertical-align:.1em}'
      . '.pad-poll-bar{grid-column:1/-1;height:.55em;border-radius:99px;background:var(--pad-poll-track);overflow:hidden}'
      . '.pad-poll-fill{display:block;height:100%;border-radius:99px;background:var(--pad-poll-accent);opacity:.55}'
      . '.pad-poll-result.is-mine .pad-poll-fill{opacity:1}'
      . '.pad-poll-total{margin:.9em 0 0;color:var(--pad-poll-muted);font-size:.9em}' );

  }

?>
