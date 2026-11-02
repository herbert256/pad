<?php

  function padDiffArgument ( $expression ) {
    if ( preg_match ( '/^\$([A-Za-z_][A-Za-z0-9_.:@]*)$/', trim ( $expression ), $match ) ) {
      $name = $match [1];
      if ( padArrayCheck ( $name ) ) {
        // The field resolver converts records to arrays for loops. Preserve an object
        // when resolving this allowed application field, including an empty object.
        $raw = str_contains ( $name, ':' ) ? NULL
          : ( str_contains ( $name, '.' ) || str_contains ( $name, '@' ) ? padFieldAt ( $name, 0 ) : padFieldLevel ( $name, 2 ) );
        return $raw instanceof stdClass ? $raw : padArrayValue ( $name );
      }
      if ( padFieldNull ( $name ) ) return NULL;
      if ( padFieldCheck ( $name ) ) return padFieldValue ( $name );
    }
    return padEval ( $expression );
  }

  function padDiffValue ( $value ) {
    try {
      // Normalise native records to JSON's object/array distinction and reject resources,
      // recursive structures, invalid UTF-8 and values JSON cannot represent.
      $text = is_string ( $value ) ? padUnprotect ( $value )
        : padUnprotect ( json_encode ( $value, JSON_THROW_ON_ERROR | JSON_PRESERVE_ZERO_FRACTION ) );
      return json_decode ( $text, FALSE, 512, JSON_THROW_ON_ERROR );
    } catch ( JsonException $error ) {
      padError ( 'a structured diff requires valid JSON or a JSON-compatible value: ' . $error->getMessage () );
      return NULL;
    }
  }

  function padDiffPointer ( $key ) {
    return str_replace ( [ '~', '/' ], [ '~0', '~1' ], (string) $key );
  }

  // A change explicitly records presence: an absent field is different from JSON null.
  // Object member order is immaterial; arrays compare by position and types stay intact.
  function padDiffJsonChanges ( $old, $new, $same = FALSE, $path = '' ) {
    $changes = [];
    $objects = $old instanceof stdClass && $new instanceof stdClass;
    $arrays = is_array ( $old ) && is_array ( $new );
    if ( $objects or $arrays ) {
      $left = (array) $old;
      $right = (array) $new;
      foreach ( array_unique ( array_merge ( array_keys ( $left ), array_keys ( $right ) ) ) as $key ) {
        $at = $path . '/' . padDiffPointer ( $key );
        $had = array_key_exists ( $key, $left );
        $has = array_key_exists ( $key, $right );
        if ( ! $had or ! $has )
          $changes [] = [ 'kind' => $had ? 'removed' : 'added', 'path' => $at,
            'before' => $left [$key] ?? NULL, 'after' => $right [$key] ?? NULL, 'had' => $had, 'has' => $has ];
        else
          array_push ( $changes, ...padDiffJsonChanges ( $left [$key], $right [$key], $same, $at ) );
      }
      if ( $same and ! $left and ! $right )
        $changes [] = [ 'kind' => 'same', 'path' => $path, 'before' => $old, 'after' => $new, 'had' => TRUE, 'has' => TRUE ];
    } elseif ( $old !== $new or $same ) {
      $changes [] = [ 'kind' => $old === $new ? 'same' : 'changed', 'path' => $path,
        'before' => $old, 'after' => $new, 'had' => TRUE, 'has' => TRUE ];
    }
    return $changes;
  }

  function padDiffRowIndex ( $rows, $key, $side ) {
    if ( ! is_array ( $rows ) or ! array_is_list ( $rows ) ) {
      padError ( "the $side of diff:rows must be a list of records" );
      return [];
    }
    $indexed = [];
    foreach ( $rows as $row ) {
      $values = $row instanceof stdClass ? (array) $row : ( is_array ( $row ) ? $row : [] );
      $id = $values [$key] ?? NULL;
      if ( ( ! is_string ( $id ) and ! is_int ( $id ) ) or (string) $id === '' ) {
        padError ( "every $side record of diff:rows requires a nonempty string or integer key '" . padMakeSafe ( $key, 30 ) . "'" );
        return [];
      }
      $index = 'key:' . $id;
      if ( array_key_exists ( $index, $indexed ) ) {
        padError ( "the $side of diff:rows contains a duplicate key '" . padMakeSafe ( $id, 30 ) . "'" );
        return [];
      }
      $indexed [$index] = $row;
    }
    return $indexed;
  }

  function padDiffRowChanges ( $old, $new, $key, $same = FALSE ) {
    if ( ! is_string ( $key ) or $key === '' ) {
      padError ( 'diff:rows requires key= naming the unique record field' );
      return [];
    }
    $left = padDiffRowIndex ( $old, $key, 'before' );
    $right = padDiffRowIndex ( $new, $key, 'after' );
    $changes = [];
    foreach ( array_unique ( array_merge ( array_keys ( $left ), array_keys ( $right ) ) ) as $id ) {
      $at = '/' . padDiffPointer ( substr ( $id, 4 ) );
      $had = array_key_exists ( $id, $left );
      $has = array_key_exists ( $id, $right );
      if ( ! $had or ! $has )
        $changes [] = [ 'kind' => $had ? 'removed' : 'added', 'path' => $at,
          'before' => $left [$id] ?? NULL, 'after' => $right [$id] ?? NULL, 'had' => $had, 'has' => $has ];
      else
        array_push ( $changes, ...padDiffJsonChanges ( $left [$id], $right [$id], $same, $at ) );
    }
    return $changes;
  }

  function padDiffStructuredHtml ( $changes, $from, $to, $kind ) {
    $html = padDiffStyle () . padDiffStructuredStyle () . '<table class="pad-diff pad-diff-structured pad-diff-' . $kind . '">'
      . '<caption>' . padDiffEscape ( $from . ' → ' . $to ) . '</caption>'
      . '<thead><tr><th scope="col">Change</th><th scope="col">Path</th><th scope="col">' . padDiffEscape ( $from )
      . '</th><th scope="col">' . padDiffEscape ( $to ) . '</th></tr></thead><tbody>';
    foreach ( $changes as $change ) {
      $html .= '<tr class="pad-diff-' . $change ['kind'] . '"><th scope="row">' . ucfirst ( $change ['kind'] )
        . '</th><td><code>' . padDiffEscape ( $change ['path'] === '' ? '(root)' : $change ['path'] ) . '</code></td>';
      foreach ( [ [ 'before', 'had', 'del' ], [ 'after', 'has', 'ins' ] ] as [ $field, $present, $mark ] ) {
        if ( ! $change [$present] ) $value = '<span class="pad-diff-absent">Absent</span>';
        else {
          $value = '<code>' . padDiffEscape ( json_encode ( $change [$field], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRESERVE_ZERO_FRACTION ) ) . '</code>';
          if ( $change ['kind'] !== 'same' ) $value = "<$mark>$value</$mark>";
        }
        $html .= '<td>' . $value . '</td>';
      }
      $html .= '</tr>';
    }
    if ( ! $changes ) $html .= '<tr><td colspan="4">No changes.</td></tr>';
    return $html . '</tbody></table>';
  }

  function padDiffStructuredStyle () {
    if ( ! padWidgetOnce ( 'style:diff-structured' ) ) return '';
    return '<style' . padStyleNonce () . '>.pad-diff-structured{border-collapse:collapse;width:100%;font:14px/1.5 system-ui,sans-serif}'
      . '.pad-diff-structured caption{text-align:left;font-weight:600;padding:.5rem}'
      . '.pad-diff-structured th,.pad-diff-structured td{text-align:left;padding:.5rem;border-bottom:1px solid var(--pad-diff-line);vertical-align:top}'
      . '.pad-diff-structured code{white-space:pre-wrap;overflow-wrap:anywhere}.pad-diff-absent{color:var(--pad-diff-muted);font-style:italic}</style>';
  }

?>
