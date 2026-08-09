<?php

  // What a property name means when a row carries a field of the same name.
  //
  // The sigil decides in expressions - the bare spelling is the property, the $-spelling is
  // the field; those cases are in expressions/conditions.php. This file pins the other
  // spellings: the tag pair resolves fields first, as it always has - it prints the field's
  // value and renders its content on every row - and the property: prefix is the spelling
  // that reaches the property from inside the level whatever the row carries.

  return [

    [ 'the tag pair resolves the field first',
      <<<'PAD'
      {data 'shp' ignore}[{"first":"F1"},{"first":"F2"}]{/data}
      {shp}{first@shp}P{/first@shp}{/shp}
      PAD,
      'F1PF2P' ],

    [ 'without the field it is the property again',
      <<<'PAD'
      {data 'shq'}["a","b"]{/data}
      {shq}{first@shq}P{/first@shq}{/shq}
      PAD,
      'P' ],

    [ 'the property: prefix is never shadowed',
      <<<'PAD'
      {data 'shr' ignore}[{"first":"F1"},{"first":"F2"}]{/data}
      {shr}{if property:first}Y{else}n{/if}{/shr}
      PAD,
      'Yn' ],

    [ 'the dollar tag form reads the field',
      <<<'PAD'
      {data 'shs' ignore}[{"first":"F1"},{"first":"F2"}]{/data}
      {shs}{$first@shs},{/shs}
      PAD,
      'F1,F2,' ],

  ];

?>
