<?php

  if ( ! isset ( $example ) or ! $example )
    $example = 'specials';

  if ( $example == 'sequences')
    $examples = sequenceDir ( APP . "seq/basic" ) ;
  else
    $examples = sequenceDir ( APP . "seq/$example" ) ;

  if ( isset ( $item ) and ! in_array ( $item, $examples) )
    $item = $examples [0];

  if ( ! isset ( $item ) or ! $item )
    $item = $examples [0];

  $title .= " - $example - $item"

?>