<?php

  // A search form that posts: four thousand words, and one word of seventy thousand
  // letters - each made a pattern PCRE refuses to compile.

  $many = [];
  for ( $i = 0; $i < 4000; $i++ )
    $many [] = 'w' . dechex ( $i * 7919 );

  $many = 'excerpting ' . implode ( ' ', $many );
  $huge = 'excerpting ' . str_repeat ( 'templates', 7800 );

?>
