<?php

  // Examples page: picks the group of examples to show and the one item out of it.
  //
  // example= names a directory of the application, so it is only used when it is one of the
  // groups the page offers - the same list examples.pad walks. Interpolating it unchecked
  // meant a ../ read the file names of any directory the server could reach, and an unknown
  // group ended the request with a 500. item= is checked against the group's own contents,
  // which it already was.

  $examples = [ 'sequences', 'specials', 'random', 'play/single', 'play/double', 'keepRemoveFlag' ];

  if ( ! isset ( $example ) or ! in_array ( $example, $examples ) )
    $example = 'specials';

  if ( $example == 'specials' )
    if ( ! isset ( $item ) or ! $item )
      $item = 'basic';

  if ( $example == 'sequences')
    $exam = sequenceDir ( APP . "basic" ) ;
  else
    $exam = sequenceDir ( APP . "$example" ) ;

  if ( ! count ( $exam ) )
    $exam = [ 'basic' ];

  if ( isset ( $item ) and ! in_array ( $item, $exam) )
    $item = $exam [0];

  if ( ! isset ( $item ) or ! $item )
    $item = $exam [0];

?>