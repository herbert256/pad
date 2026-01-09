<?php

  if ( ! isset ( $example ) or ! $example )
    $example = 'specials';

  if ( $example == 'specials' )
    if ( ! isset ( $item ) or ! $item )
      $item = 'basic';

  if ( $example == 'sequences')
    $exam = sequenceDir ( APP . "basic" ) ;
  else
    $exam = sequenceDir ( APP . "$example" ) ;

  if ( isset ( $item ) and ! in_array ( $item, $exam) )
    $item = $exam [0];

  if ( ! isset ( $item ) or ! $item )
    $item = $exam [0];

?>