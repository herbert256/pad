<?php

  // The search: a tag whose name, line, syntax or options hold every word asked for. A
  // match on the name counts most, then the line, then the rest.

  $tagsSearch = trim ( (string) padRequest ( 'q', '' ) );
  $words      = preg_split ( '/\s+/', strtolower ( $tagsSearch ), -1, PREG_SPLIT_NO_EMPTY );
  $found      = [];

  foreach ( tagsCatalog () as $name => $tag ) {

    if ( ! $words )
      break;

    $rest  = strtolower ( $tag ['syntax'] . ' ' . implode ( ' ', array_keys ( $tag ['options'] ) )
                        . ' ' . implode ( ' ', array_keys ( $tag ['parms'] ) ) );
    $about = strtolower ( $tag ['about'] );
    $score = 0;

    foreach ( $words as $word ) {

      if     ( $name === $word )                 $score += 100;
      elseif ( str_contains ( $name, $word ) )   $score += 30;
      elseif ( str_contains ( $about, $word ) )  $score += 10;
      elseif ( str_contains ( $rest, $word ) )   $score += 3;
      else { $score = 0; break; }

    }

    if ( $score )
      $found [] = tagsRow ( $tag ) + [ 'score' => $score ];

  }

  usort ( $found, fn ( $a, $b ) => $b ['score'] <=> $a ['score'] ?: strcmp ( $a ['name'], $b ['name'] ) );

  $hits = count ( $found );

?>
