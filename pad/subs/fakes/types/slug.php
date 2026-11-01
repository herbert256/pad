<?php

  // slug - The name of a page in an address: lower-case words joined by single dashes, the
  // shape the slug rule takes - in a row with a title before it that title's, as padStrSlug
  // makes it ({fake 'title, slug, paragraph', rows=3/} - the posts of a blog), else words=
  // words of lorem ipsum of its own, two to four: magna-aliqua-consequat.

  $title = padFakeInRow ( 'title' );
  $slug  = ( $title === NULL ) ? '' : padStrSlug ( $title );

  if ( $slug !== '' )
    return $slug;

  $words = padFakeOpt ( 'words' );

  return str_replace ( ' ', '-', padFakeWords ( $words === NULL ? padFakeNumber ( 2, 4 ) : max ( 1, (int) $words ) ) );

?>
