<?php

  // {collection 'blog', sort='date DESC', first=10} ... {/collection}: the Markdown files
  // of _content/blog/ as rows - their front-matter keys as fields, plus slug, body (the
  // Markdown written as HTML - print it with {!body}) and source. slug=$slug picks one
  // file, for the page that shows a post; html lets raw HTML in the bodies through. The
  // handling options - sort, first, where - work on the rows as on any data. padCollection
  // in lib/collection.php does the reading.

  if ( trim ( (string) $padParm ) === '' and $padCheckSyntax )
    padError ( "the {collection} needs a name - {collection 'blog'} reads _content/blog/" );

  return padCollection ( $padParm, padTagParm ( 'slug' ), (bool) padTagParm ( 'html' ) );

?>
