<?php

  // Normalises any value into PAD's canonical data shape - a list of occurrences, each an
  // associative array of named fields - which is what the occurrence loop iterates over.
  //
  // padData first reduces the input: NULL, FALSE, NAN, INF and empty give no occurrences,
  // TRUE gives one empty occurrence, arrays/objects/resources are taken as they are, and a
  // string is parsed by data/<type>.php with padContentType sniffing $type when not given.
  // It then runs a chain of shape fixers, each returning the data untouched unless it
  // recognises its own case:
  //
  //   padDataChkSimpleArray  a flat list becomes one named field per occurrence
  //   padDataChkChkOne       a single wrapper around a numeric list is unwrapped
  //   padDataChkDataAttr     XML attr sub-arrays are folded into their parent
  //   padDataChkCheckRecord  one record becomes a one-occurrence list
  //   padDataChkCheckArray   a vector of scalars becomes nested named occurrences
  //
  // Names for invented fields come from padDataName: the name= parameter, else
  // $padForceDataName, toData=, or the tag's own name.

  function padData ( $input, $type='', $name='' ) {

    global $padDataSetRecord;

    if     ( $input === NULL           ) $data = [];
    elseif ( $input === FALSE          ) $data = [];
    elseif ( is_float($input) && is_nan($input) ) $data = [];
    elseif ( $input === INF            ) $data = [];
    elseif ( $input === TRUE           ) $data = [ 1 => [] ];
    elseif ( is_array ( $input)        ) $data = $input;
    elseif ( is_object ( $input)       ) $data = padToArray ( $input );
    elseif ( is_resource ( $input)     ) $data = padToArray ( $input );
    elseif ( ! $input                  ) $data = [];
    elseif ( strlen(trim($input)) == 0 ) $data = [];
    else                                 $data = trim ( $input );

    if ( ! is_array ( $data ) ) {

      if ( ! $type )
        $type = padContentType ( $data );

      // A type often arrives as a file extension - of a _data/ file, or of the file name a
      // download names - and those come in more than one spelling: .yml is YAML as much as
      // .yaml is, .htm is HTML, and .CSV is .csv. The reader is data/<type>.php, so a
      // _data/pets.yml was "no data type named 'yml'" under the strict check and read as CSV
      // without it.

      if ( is_string ( $type ) ) {
        $type = strtolower ( $type );
        $type = [ 'yml' => 'yaml', 'htm' => 'html' ] [$type] ?? $type;
      }

      // The reference's data-types family: which pages a format is really parsed for. The
      // recorder only exists once the xref mode has started, hence the function test.

      if ( ( $GLOBALS ['padInfoXref'] ?? FALSE ) and function_exists ( 'padInfoXref' ) )
        padInfoXref ( 'config/data', $type );

      // A type word with no parser behind it used to end the request on a raw missing
      // include. Strict mode names it; the lenient walk falls back to sniffing the type
      // from the data, the way an untyped {data} is read.

      if ( ! file_exists ( PAD . "data/$type.php" ) ) {

        global $padCheckSyntax;

        if ( $padCheckSyntax )
          padError ( "there is no data type named '" . padMakeSafe ( $type, 40 ) . "'" );

        $type = padContentType ( $data );

      }

      $data = include PAD . "data/$type.php";

    }

    // A RECORD answer of db() is one row, which the fixers below would otherwise take for
    // a list of fields, one occurrence each. db() keeps every row it answered for RECORD,
    // and the row is known again by its value wherever it arrives. It was one flag that the
    // next padData() of the request took, whatever it was given: a second RECORD read in
    // the same PHP file, or the same record iterated twice, came out one occurrence per
    // field, and a plain list after a RECORD came out as one occurrence holding the list.

    if ( is_array ( $input ) and $input and in_array ( $input, $padDataSetRecord ?? [], TRUE ) )
      $data = padDataChkCheckRecord ($data,$name);

    $data = padDataChkSimpleArray ($data,$name);
    $data = padDataChkChkOne      ($data,$name);
    $data = padDataChkDataAttr    ($data,$name);
    $data = padDataChkCheckRecord ($data,$name);
    $data = padDataChkCheckArray  ($data,$name);

    return $data;

  }

  function padDataChkSimpleArray ($data,$name) {

    $result = $data;

    foreach ($result as $padK => $padV)
      if ( is_array($padV) )
        return $result;

    $name   = padDataName($name);
    $tmp    = $result;
    $result = [];

    foreach ($tmp as $k => $v)
      $result [$k] [$name] = $v;

    return $result;

  }

  function padDataChkCheckArray ($data,$name) {

    $result = $data;

    foreach ($result as $k => $v) {
      $x = 0 ;
      foreach ($v as $k2 => $v2)
        if ( ctype_digit( (string) $k2) and ( $k2 == $x or ($k2-1==$x) ) and ! is_array($v2) )
          $x++;
        else
          return $result;
    }

    $name = padDataName($name);

    foreach ($result as $k => $v) {
      $tmp = $v;
      $result [$k] = [];
      foreach ($tmp as $k2 => $v2)
        $result [$k] ["$name"] [$k2] ["$name"] = $v2;
    }

    return $result;

  }

  function padDataChkCheckRecord ($data,$name) {

    $result = $data;

    foreach ($result as $k => $v)
      if ( ! is_array($v) ) {
        $tmp = $result;
        $result = [];
        foreach ($tmp as $k => $v)
          $result [0] [$k] = $v;
        return $result;
      }

    return $result;

  }

  function padDataChkChkOne ($data,$name) {

    $result = $data;

    if ( count($result) == 1 and is_array($result[array_key_first($result)]) ) {

      $idx=0;
      foreach ($result[array_key_first($result)] as $key => $value) {
        if ( $key != $idx ) {
          $idx = 0;
          break;
        }
        $idx++;
      }

      if ($idx) {
        $tmp = $result[array_key_first($result)];
        $result = $tmp;
      }

    }

    return $result;

  }

  function padDataChkDataAttr ($data,$name) {

    $result = $data;

    foreach ($result as $k => $v)
      if ( is_array($v) )
        if (trim($k) == 'attr') {
          foreach ($v as $k2 => $v2)
            $result [$k2] = $v2;
          unset ($result [$k]);
        } else
          $result [$k] = padDataChkDataAttr ( $result [$k], $name );

    return $result;

  }

  function padDataName ($name) {

    global $pad, $padPrm, $padTag, $padName, $padForceDataName;

    if     ( $name                              ) $return = $name;
    elseif ( isset ($padPrm [$pad] ['name'] )   ) $return = $padPrm [$pad] ['name'];
    elseif ( $padForceDataName                  ) $return = $padForceDataName;
    elseif ( isset ($padPrm [$pad] ['toData'] ) ) $return = $padPrm [$pad] ['toData'];
    elseif ( isset ($padTag [$pad] )            ) $return = $padTag [$pad];

    if ( substr($return, 0, 1) == '$' )
      $return = substr($return, 1);

    return $return;

  }

?>