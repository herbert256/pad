<?php

  // The inputs subsystem: rich form fields.
  //
  //   {input:slider 'volume', label='Volume', min=0, max=100/}
  //   {slider 'volume', label='Volume', unit='%'/}           bare, when nothing else has the name
  //   {stars 'score', label='Your rating', rules='required'/}
  //   {combobox 'city', label='City'}
  //     Amsterdam
  //     Berlin
  //   {/combobox}
  //
  // A kind is a file of inputs/types/ - <kind>.php writes the field - and the files there are
  // the kinds there are (padInputTypes); inputs/assets/ holds its script, <kind>.js, and its
  // style, <kind>.css, beside the runtime's input.js and input.css. inputs/input.php is the
  // body the input: type runs (types/input.php); a kind's file runs in padInputTag.
  //
  // Every kind is a field of a {form} as {input} is one (lib/form.php): named by its first
  // parameter, labelled by label=, refilled from the post when its own form came back
  // (padFormValue), with the message padValidate - or the rules= of the template, checked
  // before the page's PHP - left for it (padFormError), tied to it by aria-invalid and
  // aria-describedby. Its rules= are read with {input}'s before the page's PHP runs
  // (padFormRulesOf), so a post that breaks them comes back refilled, the message beside the
  // field. The message stands after the field's frame, worded with its label - the frame
  // carries the label in data-pad-label, where the browser's checker reads it.
  //
  // The value a kind posts is a real one, and a form posts it without any script: the server
  // writes a native control - a range, a number, a date, a time, a colour, a checkbox, a
  // radio group, a file field, a text field holding a comma list or the code - and the kind's
  // script makes more of it where it runs: a calendar over the date, chips over the comma
  // list, a box per character over the code, a canvas over the hidden value of a signature.
  // The script of the inputs - assets/input.js, the runtime - and each kind's are written
  // once per page with the CSP nonce, as the interactive tags write theirs (lib/widget.php);
  // a field a fragment or a live region brings later is enhanced too.
  //
  // The items of the tag are read raw, as {input}'s are (padParmsRaw): the first without a
  // name is the field's name, a kind takes the options it knows (padInputTake), and every
  // other item is an attribute of its main control - required, placeholder=, autocomplete=
  // - written the way {attrs} writes it.

  // padInputTypes and padInputType - the kinds there are - are lib/input.php's, loaded on every
  // request: a page lists the kinds without writing one.

  // A field of one kind: its file in inputs/types/ writes it. The file runs in this function,
  // with the tag's items at hand in $padInputNow - put back afterwards, for a field written
  // while another one is (a pass a value of it starts).

  function padInputTag ( $kind, $source = '' ) {

    global $padInputNow;

    $before      = $padInputNow ?? NULL;
    $padInputNow = padInputItems ( $kind, (string) $source );

    $html = include PAD . "inputs/types/$kind.php";

    $padInputNow = $before;

    return $html;

  }

  // The items of the tag, as padFormItems reads {input}'s: the first item without a name that
  // is not a bare word is the field's name, a bare word is TRUE, an array without a name adds
  // its keys as attributes - judged as {attrs} judges them - and every named item is kept,
  // evaluated, under its name in lower case, for the kind to take or to write as an attribute.

  function padInputItems ( $kind, $source ) {

    global $pad, $padType, $padPrefix, $padCheckSyntax;

    $name  = NULL;
    $items = [];
    $extra = [];

    foreach ( padAttrsItems () as [ $key, $expr ] ) {

      $bare = padAttrsBare ( $expr );

      if ( $key === '' and ! $bare and $name === NULL ) {
        $name = (string) padEval ( $expr );
        continue;
      }

      if ( $key === '' and $bare ) {
        $items [ strtolower ( $expr ) ] = [ $expr, TRUE ];
        continue;
      }

      if ( $key === '' ) {
        $value = padEval ( $expr );
        if ( is_array ( $value ) )
          foreach ( $value as $one => $val )
            if ( padAttrsBare ( (string) $one ) and padAttrsSafe ( (string) $one, $val ) )
              $extra [ (string) $one ] = $val;
        continue;
      }

      $items [ strtolower ( $key ) ] = [ $key, padEval ( $expr ) ];

    }

    $written = ( ( $padType [$pad] ?? '' ) == 'input' and ( $padPrefix [$pad] ?? '' ) !== '' ) ? "input:$kind" : $kind;

    if ( ( $name === NULL or $name === '' ) and $padCheckSyntax )
      padError ( "the {" . $written . "} needs a field name - {" . $written . " 'volume'}" );

    return [ 'kind' => $kind, 'written' => $written, 'name' => (string) $name, 'items' => $items,
             'extra' => $extra, 'source' => $source ];

  }

  // The tag the field is written as, for what a message says: {input:slider 'volume'} or
  // {slider 'volume'}.

  function padInputWhat () {

    global $padInputNow;

    return '{' . ( $padInputNow ['written'] ?? 'input' ) . " '" . padMakeSafe ( $padInputNow ['name'] ?? '', 40 ) . "'}";

  }

  function padInputName () {

    return (string) ( $GLOBALS ['padInputNow'] ['name'] ?? '' );

  }

  // An option of the kind, taken out of the items - it is then no attribute of the control -
  // or $default when the tag does not give it.

  function padInputTake ( $option, $default = NULL ) {

    global $padInputNow;

    $key = strtolower ( $option );

    if ( ! array_key_exists ( $key, $padInputNow ['items'] ?? [] ) )
      return $default;

    $value = $padInputNow ['items'] [$key] [1];

    unset ( $padInputNow ['items'] [$key] );

    return $value;

  }

  // Whether an item is there, without taking it: min= and max= of a slider are attributes of
  // its range that the kind reads too.

  function padInputPeek ( $option, $default = NULL ) {

    return $GLOBALS ['padInputNow'] ['items'] [ strtolower ( $option ) ] [1] ?? $default;

  }

  // A bare option given or not - {datepicker 'day', monday/}.

  function padInputFlag ( $option ) {

    $value = padInputTake ( $option, FALSE );

    return ( $value !== FALSE and $value !== NULL and $value !== '' and $value !== 0 and $value !== '0' );

  }

  // A number option, held to its bounds: one that is no number, or out of them, is named under
  // the strict check and takes the default. $whole asks for a whole number.

  function padInputNumber ( $option, $default, $min = -INF, $max = INF, $whole = FALSE, $peek = FALSE ) {

    global $padCheckSyntax;

    $value = $peek ? padInputPeek ( $option, $default ) : padInputTake ( $option, $default );
    $text  = is_string ( $value ) ? trim ( $value ) : $value;

    if ( is_numeric ( $text ) and ( ! $whole or preg_match ( '/^[+-]?\d+$/D', (string) $text ) )
         and $text + 0 >= $min and $text + 0 <= $max )
      return $text + 0;

    if ( $padCheckSyntax )
      padError ( "the " . padInputWhat () . " has no usable $option '" . padMakeSafe ( is_scalar ( $value ) ? (string) $value : '', 20 )
                 . "' - " . ( $whole ? 'a whole number' : 'a number' )
                 . ( is_finite ( $min ) && is_finite ( $max ) ? " from $min to $max" : ( is_finite ( $min ) ? " of at least $min" : '' ) ) );

    return $default;

  }

  // An option that is one of a few words, named under the strict check when it is another.

  function padInputChoice ( $option, $default, $choices ) {

    global $padCheckSyntax;

    $value = padInputTake ( $option, $default );
    $value = is_string ( $value ) ? strtolower ( trim ( $value ) ) : $value;

    if ( in_array ( $value, $choices, TRUE ) )
      return $value;

    if ( $padCheckSyntax )
      padError ( "the " . padInputWhat () . " has no $option '" . padMakeSafe ( is_scalar ( $value ) ? (string) $value : '', 20 )
                 . "' - " . implode ( ', ', $choices ) );

    return $default;

  }

  // The content between the field's tags, as written - its shared indent taken off.

  function padInputSource () {

    return padChartDedent ( (string) ( $GLOBALS ['padInputNow'] ['source'] ?? '' ) );

  }

  // A list for a field - the choices of a combobox, the suggestions of a tag input, the
  // swatches of a colour picker: the option given as an array or a comma list, else the rows
  // of the store data= names (a row that is a list gives its first field, or the field named
  // by $field), else the lines of the content between the tags. Every item a trimmed text,
  // the empty ones and the second copies left out.

  function padInputList ( $option, $field = '' ) {

    $list = padInputTake ( $option, NULL );

    if ( is_string ( $list ) )
      $list = explode ( ',', $list );
    elseif ( $list === NULL and ( $rows = padInputRows () ) !== NULL ) {
      $list = [];
      foreach ( (array) $rows as $row ) {
        if ( is_object ( $row ) )
          $row = padToArray ( $row );
        if ( is_array ( $row ) )
          $row = ( $field !== '' and array_key_exists ( $field, $row ) ) ? $row [$field] : reset ( $row );
        if ( is_scalar ( $row ) )
          $list [] = (string) $row;
      }
    } elseif ( $list === NULL )
      $list = ( trim ( padInputSource () ) !== '' ) ? preg_split ( '/\R/', padInputSource () ) : [];

    $out = [];

    foreach ( (array) $list as $item )
      if ( is_scalar ( $item ) and ( $item = trim ( (string) $item ) ) !== '' )
        $out [$item] = TRUE;

    return array_map ( 'strval', array_keys ( $out ) );

  }

  // The rows a data= item names - a {data} store, a sequence store, an array of the page, a
  // field holding a list or a _data file, as a game's or a chart's data= - or NULL without it.

  function padInputRows () {

    global $padDataStore, $pqStore, $padCheckSyntax;

    $name = padInputTake ( 'data', NULL );

    if ( $name === NULL )
      return NULL;

    if ( is_array ( $name ) )
      return $name;

    $name = (string) $name;

    if ( padValidVar ( $name ) ) {

      if ( isset ( $padDataStore [$name] ) ) return $padDataStore [$name];
      if ( isset ( $pqStore      [$name] ) ) return $pqStore      [$name];

      if ( isset ( $GLOBALS [$name] ) and is_array ( $GLOBALS [$name] ) )
        return $GLOBALS [$name];

      if ( padFieldCheck ( $name ) and is_array ( $field = padFieldValue ( $name ) ) )
        return $field;

      if ( $file = padDataFileName ( $name ) )
        return padDataFileData ( $file );

    }

    if ( $padCheckSyntax and ! padStrHidden ( $name ) )
      padError ( "there is no data named '" . padMakeSafe ( $name, 40 ) . "' for the " . padInputWhat () );

    return [];

  }

  // What the field shows: the posted value when its form came back, else value= - or, for a
  // kind whose value can stand between its tags, that content.

  function padInputValue ( $default = '' ) {

    return padFormValue ( padInputName (), (string) padInputTake ( 'value', $default ) );

  }

  // The label the messages are worded with - the field's label made fit for a sentence, or
  // its name made readable - as padFormError words the server's and the browser's checker
  // reads it from data-pad-label.

  function padInputLabelText ( $label ) {

    return ( $label !== NULL and (string) $label !== '' ) ? padValidateLabel ( $label ) : padValidateName ( padInputName () );

  }

  // The field: the label, the message and the aria attributes of {input} (padFormField), the
  // rules= checked as {input}'s are (padFormRulesCheck), and the items the kind did not take
  // as attributes of its main control. $control ( $head, $rest, $label, $error, $id ) writes
  // the control - $head is its name and id, $rest its attributes - and answers its markup;
  // padInputFrame puts the frame round it.

  function padInputField ( $control ) {

    global $padInputNow;

    $name = padInputName ();
    $take = [];

    foreach ( [ 'label', 'id', 'rules' ] as $special )
      if ( array_key_exists ( $special, $padInputNow ['items'] ) )
        $take [$special] = padInputTake ( $special );

    padFormRulesCheck ( $padInputNow ['written'], $name, $take );

    // The kind writes the type of its field and the name is its first parameter: a type= or
    // name= among the items would write the attribute twice, and the browser takes the first.

    foreach ( [ 'type', 'name' ] as $own )
      if ( array_key_exists ( $own, $padInputNow ['items'] ) ) {
        unset ( $padInputNow ['items'] [$own] );
        if ( $GLOBALS ['padCheckSyntax'] )
          padError ( "the " . padInputWhat () . " writes its own $own - $own= is no item of it" );
      }

    $attrs = [];

    foreach ( $padInputNow ['extra'] as $key => $value )
      padAttrsOne ( $attrs, $key, $value );

    foreach ( $padInputNow ['items'] as [ $key, $value ] )
      padAttrsOne ( $attrs, $key, $value );

    // A field without a label is named by its name made readable, as its messages are: a
    // slider with no name a screen reader can say is a slider of nothing.

    if ( ! isset ( $take ['label'] ) and ! isset ( $attrs ['aria-label'] ) and ! isset ( $attrs ['aria-labelledby'] ) )
      padAttrsOne ( $attrs, 'aria-label', padValidateName ( $name ) );

    $id = padFormId ( $name, $take );

    $padInputNow ['attrs'] = $attrs;

    return padFormField ( $name, $take, $attrs, function ( $head, $rest, $label, $error ) use ( $control, $id, $take ) {
      return $control ( $head, $rest, $label, $error, $id, padInputLabelText ( $take ['label'] ?? NULL ) );
    } );

  }

  // The attributes a kind writes on its main control - the spellcheck of a markdown editor,
  // the autocomplete of a one-time code, the class of a star - answered for the ones the
  // template does not give: an item the author wrote is the field's, as every other
  // attribute is - written beside the kind's, the browser kept the first, the kind's, and
  // spellcheck="false" or autocomplete="address-level2" was lost. A class joins the kind's
  // own, and is taken out of $rest, the attributes the control writes after it.

  function padInputOwn ( &$rest, $own ) {

    $given = $GLOBALS ['padInputNow'] ['attrs'] ?? [];
    $out   = '';

    foreach ( $own as $name => $value )
      if ( $name == 'class' ) {
        $more = '';
        if ( isset ( $given ['class'] ) ) {
          $rest = str_replace ( ' ' . $given ['class'], '', $rest );
          if ( preg_match ( '/^[^=]+="(.+)"$/s', $given ['class'], $class ) )
            $more = " $class[1]";
        }
        $out .= " class=\"$value$more\"";
      } elseif ( ! isset ( $given [$name] ) )
        $out .= " $name=\"$value\"";

    return $out;

  }

  // The frame every kind shares: its kind, the label its messages are worded with, its
  // configuration as JSON for its script - and its style and script, once per page. The
  // message stands after the frame, where the browser's checker puts its own.

  function padInputFrame ( $kind, $worded, $inner, $error, $config = [], $tag = 'div', $extra = '' ) {

    $json = $config ? ' data-config="' . padWidgetAttr ( json_encode ( $config, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ) ) . '"' : '';

    return padInputAssets ( $kind )
         . "<$tag class=\"pad-input pad-input-$kind\" data-pad-input=\"$kind\" data-pad-label=\"" . padWidgetAttr ( $worded ) . "\"$json$extra>"
         . $inner . "</$tag>" . $error;

  }

  // The look of the inputs - assets/input.css, with the colours every kind draws in as
  // custom properties with light and dark defaults - and the look and the script of the kind,
  // once per page each, the scripts with the CSP nonce.

  function padInputAssets ( $kind ) {

    $roles = [ 'ink'    => [ '#1a1a19', '#f4f3ee' ], 'muted'  => [ '#6b6a65', '#a8a79f' ],
               'line'   => [ '#c9c8c2', '#4a4a46' ], 'field'  => [ '#ffffff', '#242422' ],
               'shade'  => [ '#f0efea', '#2c2c29' ], 'accent' => [ '#2a78d6', '#5598e7' ],
               'on'     => [ '#ffffff', '#0d1b2a' ], 'good'   => [ '#1a8a5a', '#3fbf86' ],
               'fair'   => [ '#c98a12', '#e0a83a' ], 'bad'    => [ '#d43c3b', '#ef6b6a' ],
               'star'   => [ '#e8a317', '#f2b84b' ], 'knob'   => [ '#ffffff', '#e8e7e2' ] ];

    $out = padWidgetStyle ( 'input', $roles, padFileGet ( PAD . 'inputs/assets/input.css' ) );

    if ( padWidgetOnce ( "style:input-$kind" ) and ( $css = padFileGet ( PAD . "inputs/assets/$kind.css" ) ) !== '' )
      $out .= padProtect ( '<style' . padStyleNonce () . '>' . $css . '</style>' );

    // A kind that needs no script - a switch is a checkbox the style draws - has no .js file
    // and gets none.

    $script = padFileGet ( PAD . "inputs/assets/$kind.js" );

    return $out
         . padWidgetScript ( 'input', padFileGet ( PAD . 'inputs/assets/input.js' ) )
         . ( $script !== '' ? padWidgetScript ( "input-$kind", $script ) : '' );

  }

  // A colour as a colour field holds it: #rrggbb in lower case - #rgb written out. Anything
  // else - a post that broke the color rule - is shown as it is in the code field, and the
  // colour field shows black, as it does for any value it cannot read.

  function padInputColour ( $colour ) {

    $colour = strtolower ( trim ( (string) $colour ) );

    if ( preg_match ( '/^#([0-9a-f])([0-9a-f])([0-9a-f])$/D', $colour, $one ) )
      return "#$one[1]$one[1]$one[2]$one[2]$one[3]$one[3]";

    return $colour;

  }

  // A text escaped for an attribute or an element's content, as the form fields escape theirs.

  function padInputEscape ( $text ) {

    return padFormEscape ( $text );

  }

  // A field that carries a file has no rules: a file is checked by padUpload, and the post
  // pass would judge the field's posted text, which a file field never has - required failed
  // every upload.

  function padInputNoRules ( $why ) {

    global $padInputNow;

    if ( array_key_exists ( 'rules', $padInputNow ['items'] ?? [] ) )
      padError ( "the rules of " . padInputWhat () . " are never checked - $why" );

  }

?>
