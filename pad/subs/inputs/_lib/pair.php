<?php

  // A rich field of two fields - the two ends of a range: the lower and the upper number of a
  // rangeslider, the first and the last day of a daterange. Each end is a field of the form
  // of its own, named, refilled from the post of its form and given the message padValidate
  // left for it as {pad:input} is one, the two under one legend - the field's label=, or its
  // name made readable.
  //
  //   names=   the two names, the field's name with _from and _to unless given -
  //            {input:rangeslider 'price'} posts price_from and price_to, which PHP hands the page
  //   labels=  what each end says, 'From, To' unless given
  //   value=   the two values before a post, a list or a comma text
  //
  // rules= are refused: the rules of the template are read for one field by its name, and a
  // pair posts two others - the page's PHP checks them, padValidate ( [ 'price_from' =>
  // 'required', 'price_to' => 'required' ] ), and each message stands after the frame, tied
  // to its end. Every other item is an attribute of both ends' controls - required, disabled.

  // The ends: [ [ name, id, head, rest, label, error, value ], [ ... ] ] - head is the name and
  // the id of the control, rest its attributes, label the <label> of the end, error its
  // message - for the kind's $control ( $ends, $legend, $worded ) to write.

  function padInputPair ( $control, $defaults = [ '', '' ] ) {

    global $padInputNow, $padCheckSyntax;

    $base = padInputName ();

    if ( array_key_exists ( 'rules', $padInputNow ['items'] ) )
      padInputNoRules ( "a range is two fields, posted as two names: the page's PHP checks them with padValidate" );

    $names  = padInputPairList ( 'names',  [ "{$base}_from", "{$base}_to" ] );
    $labels = padInputPairList ( 'labels', [ 'From', 'To' ] );
    $values = padInputPairList ( 'value',  $defaults, FALSE );
    $legend = padInputTake ( 'label', NULL );

    padInputTake ( 'rules' );
    padInputTake ( 'id' );

    foreach ( [ 'type', 'name' ] as $own )
      if ( array_key_exists ( $own, $padInputNow ['items'] ) ) {
        unset ( $padInputNow ['items'] [$own] );
        if ( $padCheckSyntax )
          padError ( "the " . padInputWhat () . " writes its own $own - $own= is no item of it" );
      }

    if ( $names [0] === $names [1] and $padCheckSyntax )
      padError ( "the " . padInputWhat () . " has one name for its two ends - names='low, high'" );

    $attrs = [];

    foreach ( $padInputNow ['extra'] as $key => $value )
      padAttrsOne ( $attrs, $key, $value );

    foreach ( $padInputNow ['items'] as [ $key, $value ] )
      padAttrsOne ( $attrs, $key, $value );

    $padInputNow ['attrs'] = $attrs;

    $worded = padInputLabelText ( $legend );
    $ends   = [];

    foreach ( [ 0, 1 ] as $at )
      $ends [] = padFormField ( $names [$at], [], $attrs, function ( $head, $rest, $label, $error ) use ( $names, $labels, $values, $at ) {
        $id = padFormId ( $names [$at], [] );
        return [ 'name'  => $names [$at], 'id' => $id, 'head' => $head, 'rest' => $rest,
                 'label' => '<label for="' . padInputEscape ( $id ) . '">' . padInputEscape ( $labels [$at] ) . '</label>',
                 'error' => $error, 'value' => padFormValue ( $names [$at], $values [$at] ) ];
      } );

    $shown = ( $legend !== NULL and (string) $legend !== '' ) ? padInputEscape ( $legend ) : padInputEscape ( $worded );

    return $control ( $ends, $shown, $worded );

  }

  // A list of two of an option - a list or a comma text - or the defaults. Two names or labels
  // are asked for: one of them missing is named under the strict check; two values may be
  // empty.

  function padInputPairList ( $option, $defaults, $strict = TRUE ) {

    global $padCheckSyntax;

    $given = padInputTake ( $option, NULL );

    if ( $given === NULL )
      return $defaults;

    $list = is_array ( $given ) ? array_values ( $given ) : explode ( ',', is_scalar ( $given ) ? (string) $given : '' );
    $list = array_map ( fn ( $one ) => is_scalar ( $one ) ? trim ( (string) $one ) : '', $list );

    if ( count ( $list ) == 2 and ( ! $strict or ( $list [0] !== '' and $list [1] !== '' ) ) )
      return $list;

    if ( $padCheckSyntax )
      padError ( "the " . padInputWhat () . " has no two $option '" . padMakeSafe ( is_scalar ( $given ) ? (string) $given : implode ( ', ', $list ), 30 )
                 . "' - one for each end, $option='" . implode ( ', ', $strict ? $defaults : [ 'a', 'b' ] ) . "'" );

    return $defaults;

  }

?>
