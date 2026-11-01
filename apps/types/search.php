<?php

  // The search: a type whose name, line, syntax or members hold every word asked for. A
  // match on the name counts most, then the line, then the rest.

  $typesSearch = trim ( (string) padRequest ( 'q', '' ) );
  $words       = preg_split ( '/\s+/', strtolower ( $typesSearch ), -1, PREG_SPLIT_NO_EMPTY );
  $found       = [];

  foreach ( typesCatalog () as $name => $type ) {

    if ( ! $words )
      break;

    $rest  = strtolower ( $type ['syntax'] . ' ' . implode ( ' ', array_column ( typesMembers ( $name ), 0 ) ) );
    $about = strtolower ( $type ['about'] );
    $score = 0;

    foreach ( $words as $word ) {

      if     ( $name === $word or "$name:" === $word ) $score += 100;
      elseif ( str_contains ( $name, $word ) )         $score += 30;
      elseif ( str_contains ( $about, $word ) )        $score += 10;
      elseif ( str_contains ( $rest, $word ) )         $score += 3;
      else { $score = 0; break; }

    }

    if ( $score )
      $found [] = typesRow ( $type ) + [ 'score' => $score ];

  }

  usort ( $found, fn ( $a, $b ) => $b ['score'] <=> $a ['score'] ?: strcmp ( $a ['name'], $b ['name'] ) );

  $hits = count ( $found );

?>
