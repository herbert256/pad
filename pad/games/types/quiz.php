<?php

  // quiz - questions with answers to choose from, one at a time, a point for each right one
  // and the right answer shown after a wrong one. A question is a line between the tags: the
  // question, then the answers after a |, the right one first - they are shuffled when shown.
  // By data= a row is question, answer and a comma list wrong. order keeps the questions in
  // the order written.
  //
  //   {quiz}
  //     Which tag repeats its content? | {while} | {if} | {set}
  //     What does | upper do? | makes text upper case | removes spaces
  //   {/quiz}

  $padQuizRows = padGameRows ();
  $padQuiz     = [];

  if ( $padQuizRows !== NULL ) {

    foreach ( (array) $padQuizRows as $padQuizRow ) {
      $padQuizRow = is_object ( $padQuizRow ) ? padToArray ( $padQuizRow ) : $padQuizRow;
      if ( ! is_array ( $padQuizRow ) or ! isset ( $padQuizRow ['question'], $padQuizRow ['answer'] ) ) {
        if ( $GLOBALS ['padCheckSyntax'] )
          padError ( "a row of the quiz data has no question and answer - its fields are question, answer and wrong" );
        continue;
      }
      $padQuiz [] = [ trim ( (string) $padQuizRow ['question'] ),
                      array_merge ( [ trim ( (string) $padQuizRow ['answer'] ) ],
                                    array_map ( 'trim', explode ( ',', (string) ( $padQuizRow ['wrong'] ?? '' ) ) ) ) ];
    }

  } else

    foreach ( preg_split ( '/\R/', padGameSource () ) as $padQuizLine ) {
      if ( trim ( $padQuizLine ) === '' )
        continue;
      $padQuizParts = array_map ( 'trim', explode ( '|', $padQuizLine ) );
      $padQuizParts = array_values ( array_filter ( $padQuizParts, fn ( $part ) => $part !== '' ) );
      if ( count ( $padQuizParts ) < 3 ) {
        if ( $GLOBALS ['padCheckSyntax'] )
          padError ( "the quiz line '" . padMakeSafe ( trim ( $padQuizLine ), 60 ) . "' needs a question and two answers or more - question | right answer | wrong answer" );
        continue;
      }
      $padQuiz [] = [ array_shift ( $padQuizParts ), $padQuizParts ];
    }

  foreach ( $padQuiz as $padQuizKey => $padQuizOne )
    $padQuiz [$padQuizKey] [1] = array_values ( array_filter ( $padQuizOne [1], fn ( $answer ) => $answer !== '' ) );

  $padQuiz = array_values ( array_filter ( $padQuiz, fn ( $one ) => $one [0] !== '' and count ( $one [1] ) >= 2 ) );

  if ( ! $padQuiz ) {
    if ( $GLOBALS ['padCheckSyntax'] )
      padError ( "the " . padGameWhat () . " has no questions - a line per question between its tags: question | right answer | wrong answer" );
    return '';
  }

  return padGame ( 'quiz', 'Quiz', [ 'questions' => $padQuiz, 'order' => padGameFlag ( 'order' ) ] );

?>
