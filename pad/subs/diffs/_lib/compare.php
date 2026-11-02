<?php
  include_once PAD . 'subs/diffs/_lib/text.php';

  function padDiffDocument ( $value ) {
    if ( is_string ( $value ) ) {
      if ( strlen ( $value ) > 1048576 ) throw new LengthException ( 'Diff JSON exceeds 1 MiB' );
      $decoded = json_decode ( $value, FALSE, 64, JSON_THROW_ON_ERROR );
      padDiffJsonIntegers ( $value );
      $value = $decoded;
    }
    $nodes = 0;
    padDiffValidate ( $value, $nodes );
    return $value;
  }

  // Check integer lexemes before JSON's conversion to float can lose their identity.
  // JSON was validated already. Skip quoted keys/values, including escaped quotes.
  function padDiffJsonIntegers ( $json ) {
    $length = strlen ( $json );
    for ( $i = 0; $i < $length; $i++ ) {
      if ( $json [$i] === '"' ) {
        while ( ++$i < $length and $json [$i] !== '"' ) if ( $json [$i] === '\\' ) $i++;
      } elseif ( $json [$i] === '-' or ( $json [$i] >= '0' and $json [$i] <= '9' ) ) {
        $size = strspn ( $json, '0123456789.eE+-', $i );
        $number = substr ( $json, $i, $size ); $i += $size - 1;
        if ( strpbrk ( $number, '.eE' ) !== FALSE ) continue;
        $negative = $number [0] === '-';
        $digits = $negative ? substr ( $number, 1 ) : $number;
        $bound = $negative ? substr ( (string) PHP_INT_MIN, 1 ) : (string) PHP_INT_MAX;
        if ( strlen ( $digits ) > strlen ( $bound ) or ( strlen ( $digits ) === strlen ( $bound ) and strcmp ( $digits, $bound ) > 0 ) )
          throw new InvalidArgumentException ( 'Diff JSON integer is outside the PHP integer range' );
      }
    }
  }

  function padDiffValidate ( $value, &$nodes, $depth = 0 ) {
    if ( ++$nodes > 20000 or $depth > 64 ) throw new LengthException ( 'Diff input exceeds 20000 nodes or depth 64' );
    if ( is_object ( $value ) and ! $value instanceof stdClass ) throw new InvalidArgumentException ( 'Diff accepts JSON values only' );
    if ( is_resource ( $value ) or ( is_float ( $value ) and ! is_finite ( $value ) ) ) throw new InvalidArgumentException ( 'Diff accepts finite JSON values only' );
    if ( is_string ( $value ) and ( strlen ( $value ) > 1048576 or ! preg_match ( '//u', $value ) ) ) throw new InvalidArgumentException ( 'Diff text must be UTF-8 and at most 1 MiB' );
    if ( is_array ( $value ) or is_object ( $value ) )
      foreach ( $value as $key => $child ) {
        if ( ! preg_match ( '//u', (string) $key ) ) throw new InvalidArgumentException ( 'Diff keys must be UTF-8' );
        padDiffValidate ( $child, $nodes, $depth + 1 );
      }
  }

  function padDiffValueType ( $v ) {
    return is_array ( $v ) ? ( array_is_list ( $v ) ? 'array' : 'object' ) : ( $v instanceof stdClass ? 'object' : gettype ( $v ) );
  }

  function padDiffPointer ( $key ) { return str_replace ( [ '~', '/' ], [ '~0', '~1' ], (string) $key ); }

  function padDiffWalk ( $a, $b, $path, &$out ) {
    $ta = padDiffValueType ( $a ); $tb = padDiffValueType ( $b );
    if ( $ta === $tb and in_array ( $ta, [ 'array', 'object' ], TRUE ) ) {
      $a = (array) $a; $b = (array) $b;
      $keys = array_unique ( array_merge ( array_keys ( $a ), array_keys ( $b ) ), SORT_STRING );
      if ( $ta === 'object' ) usort ( $keys, fn ( $x, $y ) => strcmp ( (string) $x, (string) $y ) );
      foreach ( $keys as $key ) {
        $here = $path . '/' . padDiffPointer ( $key );
        if ( ! array_key_exists ( $key, $a ) ) $out [] = [ 'op' => 'add', 'path' => $here, 'after' => $b [$key] ];
        elseif ( ! array_key_exists ( $key, $b ) ) $out [] = [ 'op' => 'remove', 'path' => $here, 'before' => $a [$key] ];
        else padDiffWalk ( $a [$key], $b [$key], $here, $out );
      }
    } elseif ( $a !== $b ) $out [] = [ 'op' => 'replace', 'path' => $path, 'before' => $a, 'after' => $b ];
  }

  function padDiffJson ( $a, $b ) {
    $a = padDiffDocument ( $a ); $b = padDiffDocument ( $b ); $out = [];
    padDiffWalk ( $a, $b, '', $out );
    return $out;
  }

  function padDiffRows ( $a, $b, $options ) {
    $key = $options ['key'] ?? NULL;
    if ( ! is_string ( $key ) or $key === '' ) throw new InvalidArgumentException ( 'Row diff needs a stable key field' );
    $maps = [];
    foreach ( [ $a, $b ] as $rows ) {
      if ( ! is_array ( $rows ) or ! array_is_list ( $rows ) ) throw new InvalidArgumentException ( 'Row diff needs lists of rows' );
      $nodes = 0; padDiffValidate ( $rows, $nodes ); $map = [];
      foreach ( $rows as $row ) {
        if ( ! is_array ( $row ) or ! array_key_exists ( $key, $row ) or ( ! is_string ( $row [$key] ) and ! is_int ( $row [$key] ) ) or $row [$key] === '' )
          throw new InvalidArgumentException ( 'Each row needs a nonempty string or integer key' );
        $id = ( is_int ( $row [$key] ) ? 'i:' : 's:' ) . $row [$key];
        if ( array_key_exists ( $id, $map ) ) throw new InvalidArgumentException ( 'Duplicate row key: ' . $row [$key] );
        $map [$id] = $row;
      }
      $maps [] = $map;
    }
    [ $a, $b ] = $maps; $out = [];
    foreach ( array_unique ( array_merge ( array_keys ( $a ), array_keys ( $b ) ) ) as $id ) {
      $changes = []; $path = '/' . padDiffPointer ( $id );
      if ( ! array_key_exists ( $id, $a ) ) $changes [] = [ 'op' => 'add', 'path' => $path, 'after' => $b [$id] ];
      elseif ( ! array_key_exists ( $id, $b ) ) $changes [] = [ 'op' => 'remove', 'path' => $path, 'before' => $a [$id] ];
      else padDiffWalk ( (object) $a [$id], (object) $b [$id], $path, $changes );
      foreach ( $changes as $change ) { $change ['key'] = ( $a [$id] ?? $b [$id] ) [$key]; $out [] = $change; }
    }
    return $out;
  }

  function padDiffTextRecords ( $a, $b, $lines ) {
    if ( ! is_string ( $a ) or ! is_string ( $b ) ) throw new InvalidArgumentException ( 'Text diff needs strings' );
    $nodes = 0; padDiffValidate ( $a, $nodes ); padDiffValidate ( $b, $nodes );
    $split = fn ( $v ) => $lines ? ( $v === '' ? [] : preg_split ( '/\R/u', $v ) ) : padDiffTokens ( $v );
    $aa = $split ( $a ); $bb = $split ( $b );
    if ( count ( $aa ) + count ( $bb ) > 20000 ) throw new LengthException ( 'Text diff exceeds 20000 tokens' );
    $out = []; $old = $new = 0;
    foreach ( padDiffOps ( $aa, $bb ) as $op ) {
      if ( $op [0] === '=' ) { $old++; $new++; continue; }
      $out [] = [ 'op' => $op [0] === '-' ? 'remove' : 'add', 'path' => '/' . ( $op [0] === '-' ? $old++ : $new++ ),
                   $op [0] === '-' ? 'before' : 'after' => $op [1] ];
    }
    return $out;
  }

  function padDiffStructuredHtml ( $changes, $side, $from, $to ) {
    $html = padDiffStyle () . '<table class="pad-diff pad-diff-lines"><thead><tr><th scope="col">Path</th>';
    $html .= $side ? '<th scope="col">' . padDiffEscape ( $from ?: 'Before' ) . '</th><th scope="col">' . padDiffEscape ( $to ?: 'After' ) . '</th>' : '<th scope="col">Change</th>';
    $html .= '</tr></thead><tbody>';
    $show = fn ( $v ) => padDiffEscape ( json_encode ( $v, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRESERVE_ZERO_FRACTION | JSON_THROW_ON_ERROR ) );
    foreach ( $changes as $change ) {
      $old = array_key_exists ( 'before', $change ) ? '<del>' . $show ( $change ['before'] ) . '</del>' : '';
      $new = array_key_exists ( 'after', $change ) ? '<ins>' . $show ( $change ['after'] ) . '</ins>' : '';
      $html .= '<tr><th scope="row">' . padDiffEscape ( $change ['path'] === '' ? '(root)' : $change ['path'] ) . '</th><td>' . $old . ( $side ? '</td><td>' : ' ' ) . $new . '</td></tr>';
    }
    if ( ! $changes ) $html .= '<tr><td colspan="' . ( $side ? 3 : 2 ) . '">No changes</td></tr>';
    return $html . '</tbody></table>';
  }
?>
