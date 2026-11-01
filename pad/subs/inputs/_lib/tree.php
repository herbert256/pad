<?php

  // The nodes of a treepicker (types/treepicker.php): [ [ 'value' => ..., 'text' => ...,
  // 'kids' => [ nodes ] ], ... ] - read from the paths of options=, the rows of data= or the
  // lines between its tags. A value names one item: a second item of a value is named under the strict check
  // and left out without it, as is a row whose parent is no row and the rows of a cycle,
  // which no root reaches. A root's parent is NULL in the maps below, so a key is looked up
  // with array_key_exists, never isset.

  // The rows of a store: flat, each naming its parent - parent= the field, 'parent' unless
  // given, empty for a root - or nested, its children a list in the field children=. The
  // value of a row is its key= field, its id unless given, else its text; the text its field=,
  // else the first of name, label, title and text it has, else its value. A row that is a
  // text is a root of that text.

  function padInputTreeRows ( $rows, $key, $parent, $field, $kids ) {

    $flat = [];

    padInputTreeFlat ( $flat, (array) $rows, NULL, $key, $parent, $field, $kids );

    return padInputTreeBuild ( $flat );

  }

  function padInputTreeFlat ( &$flat, $rows, $above, $key, $parent, $field, $kids ) {

    foreach ( $rows as $row ) {

      if ( is_object ( $row ) )
        $row = padToArray ( $row );

      if ( is_scalar ( $row ) ) {
        if ( trim ( (string) $row ) !== '' )
          $flat [] = [ trim ( (string) $row ), trim ( (string) $row ), $above ];
        continue;
      }

      if ( ! is_array ( $row ) )
        continue;

      $text = NULL;

      if ( $field !== '' )
        $text = $row [$field] ?? NULL;
      else
        foreach ( [ 'name', 'label', 'title', 'text' ] as $one )
          if ( isset ( $row [$one] ) and is_scalar ( $row [$one] ) ) {
            $text = $row [$one];
            break;
          }

      $value = ( $key !== '' ) ? ( $row [$key] ?? NULL ) : ( $row ['id'] ?? $text );
      $text  = $text ?? $value;

      if ( ! is_scalar ( $value ) or trim ( (string) $value ) === '' ) {
        padInputTreeFault ( "has a row without its " . ( $key !== '' ? "$key" : 'id or text' ) );
        continue;
      }

      $value = trim ( (string) $value );
      $up    = $above;

      if ( $up === NULL and isset ( $row [$parent] ) and is_scalar ( $row [$parent] ) and trim ( (string) $row [$parent] ) !== '' )
        $up = trim ( (string) $row [$parent] );

      $flat [] = [ $value, is_scalar ( $text ) ? trim ( (string) $text ) : $value, $up ];

      if ( isset ( $row [$kids] ) and is_array ( $row [$kids] ) )
        padInputTreeFlat ( $flat, $row [$kids], $value, $key, $parent, $field, $kids );

    }

  }

  // The lines between the tags, their shared indent taken off: a line indented deeper than
  // the one before it is its child, a tab four spaces. A line's text is its value.

  function padInputTreeLines ( $source ) {

    $flat  = [];
    $stack = [];

    foreach ( preg_split ( '/\R/', $source ) as $line ) {

      if ( trim ( $line ) === '' )
        continue;

      $indent = strlen ( str_replace ( "\t", '    ', $line ) ) - strlen ( ltrim ( str_replace ( "\t", '    ', $line ) ) );
      $text   = trim ( $line );

      while ( $stack and end ( $stack ) [0] >= $indent )
        array_pop ( $stack );

      $flat []  = [ $text, $text, $stack ? end ( $stack ) [1] : NULL ];
      $stack [] = [ $indent, $text ];

    }

    return padInputTreeBuild ( $flat );

  }

  // The paths of options= - 'Europe > France > Paris, Europe > France > Lyon', a list or a comma
  // text - each step a node under the one before it, a step the paths share one node. A
  // node's text is its value.

  function padInputTreePaths ( $options ) {

    $flat = [];
    $made = [];

    foreach ( is_array ( $options ) ? $options : explode ( ',', (string) $options ) as $one ) {

      if ( ! is_scalar ( $one ) )
        continue;

      $steps = array_values ( array_filter ( array_map ( 'trim', explode ( '>', (string) $one ) ), 'strlen' ) );
      $above = NULL;
      $path  = '';

      foreach ( $steps as $step ) {
        $path .= "\0$step";
        if ( ! isset ( $made [$path] ) ) {
          $made [$path] = TRUE;
          $flat []      = [ $step, $step, $above ];
        }
        $above = $step;
      }

    }

    return padInputTreeBuild ( $flat );

  }

  // [ value, text, parent value or NULL ] rows made a tree, from the roots down: a second
  // value, a parent that is none and a cycle are the author's faults.

  function padInputTreeBuild ( $flat ) {

    $seen = [];
    $kids = [];
    $text = [];

    foreach ( $flat as [ $value, $shown, $up ] ) {
      if ( array_key_exists ( $value, $seen ) ) {
        padInputTreeFault ( "has '" . padMakeSafe ( $value, 30 ) . "' twice - a value names one item" );
        continue;
      }
      $seen [$value] = $up;
      $text [$value] = $shown;
    }

    foreach ( $seen as $value => $up ) {
      if ( $up !== NULL and ! array_key_exists ( $up, $seen ) ) {
        padInputTreeFault ( "has '" . padMakeSafe ( (string) $value, 30 ) . "' under '" . padMakeSafe ( (string) $up, 30 ) . "', which is none of its rows" );
        continue;
      }
      $kids [ $up ?? "\0root" ] [] = (string) $value;
    }

    $reached = [];
    $tree    = padInputTreeWalk ( $kids, $text, "\0root", $reached );

    // What no root reached is under a parent that is none - named above - or in a cycle: its
    // parents, followed up, come back to one of them.

    $left = [];

    foreach ( array_diff ( array_map ( 'strval', array_keys ( $seen ) ), array_map ( 'strval', array_keys ( $reached ) ) ) as $one ) {
      $up = [];
      for ( $at = $one; $at !== NULL and array_key_exists ( $at, $seen ) and ! isset ( $up [$at] ); $at = $seen [$at] )
        $up [$at] = TRUE;
      if ( $at !== NULL and isset ( $up [$at] ) )
        $left [] = $one;
    }

    if ( $left )
      padInputTreeFault ( "has rows no root reaches - a cycle of parents: '" . padMakeSafe ( implode ( "', '", array_slice ( $left, 0, 4 ) ), 60 ) . "'" );

    return $tree;

  }

  function padInputTreeWalk ( $kids, $text, $at, &$reached ) {

    $nodes = [];

    foreach ( $kids [$at] ?? [] as $value ) {
      if ( isset ( $reached [$value] ) )
        continue;
      $reached [$value] = TRUE;
      $nodes [] = [ 'value' => $value, 'text' => $text [$value], 'kids' => padInputTreeWalk ( $kids, $text, $value, $reached ) ];
    }

    return $nodes;

  }

  function padInputTreeFault ( $what ) {

    if ( $GLOBALS ['padCheckSyntax'] )
      padError ( "the " . padInputWhat () . " $what" );

  }

  // The nodes as nested lists of native radio buttons - the field a form posts without any
  // script - the first with the field's id, the others the id with their place. With leaves
  // a node with children is a text of the list, no choice.

  function padInputTreeList ( $nodes, $name, $id, $chosen, $rest, $class, $leaves, &$count, &$path, $above = [] ) {

    $out = '';

    foreach ( $nodes as $node ) {

      $here = array_merge ( $above, [ $node ['text'] ] );
      $text = padInputEscape ( $node ['text'] );

      if ( $leaves and $node ['kids'] )
        $item = "<span class=\"pi-tree-text\">$text</span>";
      else {
        $count++;
        $box  = ( $count == 1 ) ? $id : "$id-$count";
        $on   = ( $node ['value'] === $chosen );
        if ( $on )
          $path = $here;
        $item = "<input type=\"radio\"$class name=\"$name\" id=\"" . padInputEscape ( $box ) . '" value="' . padInputEscape ( $node ['value'] ) . '"'
              . ( $on ? ' checked' : '' ) . "$rest><label for=\"" . padInputEscape ( $box ) . "\">$text</label>";
      }

      $out .= "<li>$item"
            . ( $node ['kids'] ? '<ul>' . padInputTreeList ( $node ['kids'], $name, $id, $chosen, $rest, $class, $leaves, $count, $path, $here ) . '</ul>' : '' )
            . '</li>';

    }

    return $out;

  }

?>
