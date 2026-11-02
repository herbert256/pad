<?php

  // padFake ( 'slug' ) passes the slug rule of the rules subsystem - its own check, read from
  // its file - with words= words or two to four; in a row with a title it is padStrSlug of
  // that title, whatever words= says; padFake ( 'title' ) is two to five words, words= of
  // them when said, a capital first and no full stop; the same seed the same text.

  $rule = include PAD . 'subs/inputs/rules/types/slug.php';

  padFakeSeed ( 8 );

  $slugs = $own = $titles = 0;
  $sizes = [];

  for ( $i = 0; $i < 500; $i++ ) {

    $slug  = padFake ( 'slug' );
    $title = padFake ( 'title' );

    $slugs  += ( $rule ['check'] ( $slug ) and substr_count ( $slug, '-' ) >= 1 and substr_count ( $slug, '-' ) <= 3 ) ? 1 : 0;
    $titles += ( preg_match ( '/^[A-Z][a-z]*( [a-z]+)*$/D', $title ) and ! str_ends_with ( $title, '.' ) ) ? 1 : 0;
    $sizes [ substr_count ( $title, ' ' ) + 1 ] = TRUE;

  }

  foreach ( padFakeRows ( 'title, slug', 50, 3, [ 'words' => 4 ] ) as $row )
    $own += ( $row ['slug'] === padStrSlug ( $row ['title'] ) and $rule ['check'] ( $row ['slug'] ) and substr_count ( $row ['title'], ' ' ) == 3 ) ? 1 : 0;

  ksort ( $sizes );

  padFakeSeed ( 'p' );  $first  = padFake ( 'title' ) . padFake ( 'slug' );
  padFakeSeed ( 'p' );  $second = padFake ( 'title' ) . padFake ( 'slug' );

  $r = json_encode ( [
    'slugs'  => $slugs,
    'titles' => $titles,
    'sizes'  => array_keys ( $sizes ),
    'rows'   => $own,
    'words'  => [ substr_count ( padFake ( 'slug', [ 'words' => 6 ] ), '-' ), substr_count ( padFake ( 'title', [ 'words' => 1 ] ), ' ' ) ],
    'seed'   => $first === $second,
  ] );

?>
