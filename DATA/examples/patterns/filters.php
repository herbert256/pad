<?php

  // The image filters of {pad:img} - the files of pad/subs/filters/types/, as the engine reads them
  // - each on the same picture, www/patterns/photos/tulips.jpg, with the template that makes
  // it: the example the filter's own file gives, its picture this application's, and the
  // first sentence of that file's comment as its line. A placeholder is shown with the
  // picture made transparent, so what stands behind it while it loads is what shows.
  //
  // The examples have no playground icon: the picture is a file of this application's www/,
  // which a template run elsewhere does not have.

  $title   = 'Image filters';
  $filters = [];

  foreach ( padKinds ( 'filters' ) as $filter ) {

    list ( $text, $example ) = patternsComment ( PAD . "subs/filters/types/$filter.php", $filter );

    $spec     = include PAD . "subs/filters/types/$filter.php";
    $template = str_replace ( [ "'photos/harbour.jpg'", 'width=400' ], [ "'photos/tulips.jpg'", 'width=360' ], $example );
    $template = preg_replace ( "/alt='[^']*'/", "alt='Rows of tulips running to a windmill'", $template );

    $filters [] = [ 'filter'   => $filter,
                    'line'     => ucfirst ( rtrim ( preg_split ( '/(?<=\.)\s/', $text, 2 ) [0], '.' ) ) . '.',
                    'template' => $template,
                    'source'   => padHighlightTokens ( $template, 'pad' ),
                    'behind'   => ( $spec ['form'] == 'placeholder' ) ? 1 : 0 ];

  }

?>
