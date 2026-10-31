<?php

  // taginput - a few words as a comma list: a native text field that posts the list as it is
  // typed - 'pad, php, forms' - and where the script runs a chip per word with a button that
  // takes it out, a word added with Enter or a comma, the last one taken back with Backspace
  // in the empty field. The list the form posts is then a hidden field, joined with ', ', the
  // same text a field without the script posts. suggestions= - a list, a store named by
  // data= or the lines between the tags - are offered as the field is typed in (a datalist);
  // most= the most words it takes, a word past it is not added.

  $padInputSuggest = padInputList ( 'suggestions' );
  $padInputMost    = padInputPeek ( 'most', NULL ) === NULL ? 0 : padInputNumber ( 'most', 0, 1, 1000, TRUE );

  $padInputNow ['value'] = padInputValue ( '' );

  return padInputField ( function ( $head, $rest, $label, $error, $id, $worded ) use ( $padInputSuggest, $padInputMost ) {

    global $padInputNow;

    $list = '';

    if ( $padInputSuggest ) {
      $list = '<datalist id="' . padInputEscape ( "$id-suggest" ) . '">';
      foreach ( $padInputSuggest as $one )
        $list .= '<option value="' . padInputEscape ( $one ) . '"></option>';
      $list .= '</datalist>';
      $rest .= ' list="' . padInputEscape ( "$id-suggest" ) . '"';
    }

    return padInputFrame ( 'taginput', $worded,
             $label
           . '<div class="pi-tags">'
           . "<input type=\"text\" $head value=\"" . padInputEscape ( $padInputNow ['value'] ) . "\" autocomplete=\"off\"$rest>"
           . '</div>' . $list
           . '<span class="pi-hide pi-tags-said" aria-live="polite"></span>',
             $error, [ 'most' => $padInputMost ] );

  } );

?>
