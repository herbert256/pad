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

  // The numbers of YAML 1.2, as yaml_parse callbacks: libyaml reads YAML 1.1, where every
  // n:nn is base 60 and a leading 0 octal - an agenda's 9:30 was 570, 12:00 was 720, the zip
  // code 02134 was 1116, 1_000 was 1000. A number is written in the YAML 1.2 forms only;
  // anything else stays the text written, and so does a number with a leading 0, which would
  // lose that 0. Used by data/yaml.php and the front matter of a collection.

  function padYamlNumbers () {

    return [

      'tag:yaml.org,2002:int'   => function ( $value ) {
        $value = (string) $value;
        if ( preg_match ( '/^[-+]?(0|[1-9][0-9]*)$/D', $value ) ) return $value + 0;
        if ( preg_match ( '/^0x[0-9a-fA-F]+$/D',        $value ) ) return hexdec ( $value );
        return $value;
      },

      'tag:yaml.org,2002:float' => function ( $value ) {
        $value = (string) $value;
        if ( preg_match ( '/^[-+]?(\.[0-9]+|(0|[1-9][0-9]*)(\.[0-9]*)?)([eE][-+]?[0-9]+)?$/D', $value ) ) return (float) $value;
        if ( preg_match ( '/^[-+]?\.(inf|Inf|INF)$/D', $value ) ) return ( $value [0] == '-' ) ? -INF : INF;
        if ( preg_match ( '/^\.(nan|NaN|NAN)$/D',       $value ) ) return NAN;
        return $value;
      }

    ];

  }

  function padData ( $input, $type='', $name='' ) {

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
    else                                 $data = padDataText ( $input );

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

      // A reader that could not parse its input answers FALSE through padError. Under the
      // error actions that carry on - 'log', 'ignore' - padData used to walk that FALSE
      // through the shape fixers below, so one "JSON conversion error" was followed by two
      // "foreach() argument must be of type array, false given" from inside the engine. A
      // reader that gave no array gives no rows.

      if ( ! is_array ( $data ) )
        $data = [];

    }

    // A row that is an object - what json_decode answers by default, PDO's FETCH_OBJ, a
    // model - is the row its properties make (padDataObject). Taken as a value, it was
    // wrapped under the tag's name: {decoded}{$a}{/decoded} was "not found", where= and sort=
    // saw no field, and {people}({$name}){/people} printed the tag's name for every row. A
    // date object, or one that writes itself as text, is a value and stays one.

    foreach ( $data as $padDataKey => $padDataRow )
      if ( is_object ( $padDataRow ) and ! $padDataRow instanceof DateTimeInterface and ! $padDataRow instanceof Stringable )
        $data [$padDataKey] = padDataObject ( $padDataRow );

    // A RECORD answer of db() is one row, which the fixers below would otherwise take for
    // a list of fields, one occurrence each. db() keeps every row it answered for RECORD, and
    // a row with its columns and some of its values is known again wherever it arrives. It was one
    // flag that the next padData() of the request took, whatever it was given: a second
    // RECORD read in the same PHP file, or the same record iterated twice, came out one
    // occurrence per field, and a plain list after a RECORD came out as one occurrence
    // holding the list. Matched by its whole value for a while, a record the page's PHP had
    // added a field to, or cast a value of, was no record any more.

    if ( padDataIsRecord ( $input ) )
      $data = padDataChkCheckRecord ($data,$name);

    $data = padDataChkSimpleArray ($data,$name);
    $data = padDataChkChkOne      ($data,$name);
    $data = padDataChkDataAttr    ($data,$name);
    $data = padDataChkCheckRecord ($data,$name);
    $data = padDataChkCheckArray  ($data,$name);

    return $data;

  }

  // An object as the array it holds - what a JsonSerializable answers, the items of a
  // Traversable, the public properties of any other - and the objects inside it the same way,
  // so a nested record reads as it does in decoded JSON: {$address.city}, {tags}...{/tags}. A
  // date object and one that writes itself as text are values and stay; an object met again
  // on its own way down - a model pointing back at its parent - stays the object it is, where
  // the walk would never end.
  //
  // An object that says what its data is - jsonSerialize - is that data, as json_encode
  // writes it: read by its public properties, a model that leaves its password hash out of
  // its JSON had it written by {^user}, the JSON x-data="{^user}" hands the browser.

  function padDataObject ( $value, $above = [] ) {

    if ( is_object ( $value ) ) {

      $id = spl_object_id ( $value );

      if ( $value instanceof DateTimeInterface or $value instanceof Stringable or isset ( $above [$id] ) )
        return $value;

      $above [$id] = TRUE;

      if ( $value instanceof JsonSerializable ) {
        $value = $value -> jsonSerialize ();
        if ( is_object ( $value ) )
          return padDataObject ( $value, $above );
      } else
        $value = ( $value instanceof Traversable ) ? iterator_to_array ( $value ) : get_object_vars ( $value );

    }

    if ( is_array ( $value ) )
      foreach ( $value as $key => $item )
        if ( is_object ( $item ) or is_array ( $item ) )
          $value [$key] = padDataObject ( $item, $above );

    return $value;

  }

  // Whether a value is a row db() answered for RECORD: named fields holding at least the
  // columns of one of them.

  // The text of a string as padData reads it: trimmed, and a UTF-8 byte order mark that leads
  // it taken off with the white space behind it. A BOM leads a string that an editor or a
  // download saved as UTF-8: it is no whitespace, so trim leaves it, and it made the first
  // CSV column "\xEF\xBB\xBFname" and pushed an XML document's <?xml off the start, while a
  // JSON or XML string behind it was not recognised on sight and read as CSV. The guards that
  // refuse a value reading as a PAD list - {data 'd', $v}, data=$v, a fetched body - judge
  // this same text, so white space before the BOM cannot hide the list from them.

  function padDataText ( $text ) {

    $text = trim ( (string) $text );

    if ( str_starts_with ( $text, "\xEF\xBB\xBF" ) )
      $text = ltrim ( substr ( $text, 3 ) );

    return $text;

  }

  function padDataIsRecord ( $input ) {

    global $padDataSetRecord;

    if ( ! is_array ( $input ) or ! $input or array_is_list ( $input ) )
      return FALSE;

    // Every column of an answered row, and at least one of them still holding that row's value
    // (loosely - a cast value is the same value). By the columns alone, a key => label map, a
    // heading list or a settings array that merely shared their names was one record after
    // any RECORD of the request: [ 'officeCode' => 'Number', 'city' => 'City' ] iterated as
    // one occurrence with three fields, where it is three - also when the names came in the
    // row's own order, [ 'name' => 'Name', 'salary' => 'Salary' ], the commonest heading list.
    // Exactly the row's columns with every value empty is the row too: a record that had
    // none. Exactly the row's columns with a number the page formatted for show - grouped,
    // rounded - is the row when that number is one of the row's own: number_format took
    // '6000' to '6,000.00', no longer loosely the same, and a record of totals stopped being
    // one the day they all passed a thousand, where '600.00' still was. A record whose every
    // value the page rewrote otherwise - a name given a capital - cannot be told from a heading
    // list; handed over as a list of one row - [ $bob ] - it is the row it was.
    // The rows are kept by their columns (padDataKeepRecord), so this is a test per column
    // list, not per row read: a page that read its orders one RECORD at a time made every
    // padData of an associative array after it walk the thousand rows kept.

    foreach ( $padDataSetRecord ?? [] as $set ) {

      if ( array_diff_key ( $set ['columns'], $input ) )
        continue;

      $exact = array_keys ( $input ) === array_keys ( $set ['columns'] );

      if ( $exact and ! array_filter ( $input, fn ( $value ) => $value !== NULL and $value !== '' ) )
        return TRUE;

      foreach ( $set ['values'] as $key => $values )
        if ( is_scalar ( $input [$key] ) and isset ( $values [ padDataLoose ( $input [$key] ) ] ) )
          return TRUE;

      if ( $exact )
        foreach ( $set ['values'] as $key => $values )
          if ( padDataFormatted ( $input [$key], $values ) )
            return TRUE;

    }

    return FALSE;

  }

  // Whether a value reads as a number written for show - grouped by thousands, given its
  // decimals: '6,000.00' or '6.000,00' (number_format's two forms), '2,333.33' - and one of
  // the kept values of its column, rounded to those decimals, is that number. No heading
  // label reads as such a number, so a list of labels stays a list.

  function padDataFormatted ( $value, $values ) {

    if ( ! is_string ( $value ) )
      return FALSE;

    $value = trim ( $value );
    $reads = [];

    if ( preg_match ( '/^(-?(?:\d{1,3}(?:,\d{3})+|\d+))(?:\.(\d+))?$/', $value, $m ) )
      $reads [] = [ str_replace ( ',', '', $m [1] ) . ( isset ( $m [2] ) ? '.' . $m [2] : '' ), strlen ( $m [2] ?? '' ) ];

    if ( preg_match ( '/^(-?(?:\d{1,3}(?:\.\d{3})+|\d+))(?:,(\d+))?$/', $value, $m ) )
      $reads [] = [ str_replace ( '.', '', $m [1] ) . ( isset ( $m [2] ) ? '.' . $m [2] : '' ), strlen ( $m [2] ?? '' ) ];

    foreach ( $reads as [ $number, $decimals ] )
      foreach ( $values as $loose => $count )
        if ( $loose [0] == 'n' and round ( (float) substr ( $loose, 1 ), $decimals ) == round ( (float) $number, $decimals ) )
          return TRUE;

    return FALSE;

  }

  // Keeps a row db() answered for RECORD, for padDataIsRecord: with the other rows of its
  // column list, and for each column the values its rows held, counted - NULL and '' are no
  // value to know a row by. The last thousand rows, about: the oldest of the column list
  // read first goes.

  function padDataKeepRecord ( $row ) {

    global $padDataSetRecord, $padDataSetRecordRows;

    $list = implode ( "\x1F", array_keys ( $row ) );
    $id   = md5 ( serialize ( $row ) );

    if ( isset ( $padDataSetRecord [$list] ['rows'] [$id] ) )
      return;

    $padDataSetRecord [$list] ['columns'] ??= array_fill_keys ( array_keys ( $row ), TRUE );
    $padDataSetRecord [$list] ['values']  ??= [];

    $keep = [];

    foreach ( $row as $key => $value )
      if ( $value !== NULL and $value !== '' and is_scalar ( $value ) ) {
        $loose = padDataLoose ( $value );
        $padDataSetRecord [$list] ['values'] [$key] [$loose] = ( $padDataSetRecord [$list] ['values'] [$key] [$loose] ?? 0 ) + 1;
        $keep [$key] = $loose;
      }

    $padDataSetRecord [$list] ['rows'] [$id] = $keep;

    if ( ( $padDataSetRecordRows = ( $padDataSetRecordRows ?? 0 ) + 1 ) <= 1000 )
      return;

    $list = array_key_first ( $padDataSetRecord );
    $id   = array_key_first ( $padDataSetRecord [$list] ['rows'] );

    foreach ( $padDataSetRecord [$list] ['rows'] [$id] as $key => $loose )
      if ( --$padDataSetRecord [$list] ['values'] [$key] [$loose] < 1 )
        unset ( $padDataSetRecord [$list] ['values'] [$key] [$loose] );

    unset ( $padDataSetRecord [$list] ['rows'] [$id] );

    if ( ! $padDataSetRecord [$list] ['rows'] )
      unset ( $padDataSetRecord [$list] );

    $padDataSetRecordRows--;

  }

  // A value as the key it is loosely equal under, as == has it: a number, or numeric text, as
  // the number it is - 7, '7', '007', 7.0 and '7.00' are one - other text as itself.

  function padDataLoose ( $value ) {

    if ( is_bool ( $value ) )
      $value = (int) $value;

    if ( ! is_numeric ( $value ) )
      return "s$value";

    $value = $value + 0;

    if ( is_float ( $value ) and $value == floor ( $value ) and abs ( $value ) < 9.2e18 )
      $value = (int) $value;

    return 'n' . ( is_int ( $value ) ? $value : sprintf ( '%.17g', $value ) );

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
