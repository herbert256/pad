<?php

  // {poll 'favorite-language', options='PHP, Python, Ruby, Go', title='Your favourite language?'}
  // - a vote among the answers of options=, once per visitor, and then the results as bars
  // with their percentages. The form posts in the background through a {live} region, or as
  // a plain form with scripting off; the votes are kept in DATA/poll/<app>/<name>.json under
  // a lock. title= is the question (the name, readable, when not given), button= the
  // button's text ('Vote'), results shows the results before a vote too. lib/poll.php.

  $padPollName = (string) $padParm;

  if ( ! preg_match ( '/^[A-Za-z0-9_-]{1,64}$/D', $padPollName ) )
    return padError ( "a {poll} needs a name of letters, digits, _ and -, like {poll 'favorite-language'}" );

  $padPollAnswers = array_values ( array_unique ( array_filter (
                      array_map ( 'trim', explode ( ',', (string) padTagParm ( 'options' ) ) ), 'strlen' ) ) );

  if ( count ( $padPollAnswers ) < 2 ) {
    if ( $padCheckSyntax )
      padError ( "the {poll '$padPollName'} needs two answers or more - options='Yes, No'" );
    return '';
  }

  return padPoll ( $padPollName, $padPollAnswers,
                   (string) padTagParm ( 'title', ucfirst ( str_replace ( [ '-', '_' ], ' ', $padPollName ) ) ),
                   (string) padTagParm ( 'button', 'Vote' ),
                   (bool)   padTagParm ( 'results', FALSE ) );

?>
