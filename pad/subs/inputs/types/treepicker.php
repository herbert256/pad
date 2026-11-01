<?php

  // treepicker - one item of a hierarchy, a category of a category, a place in a country:
  // nested lists of native radio buttons in a fieldset whose legend is the label, which a
  // form posts without any script, and where the script runs a button showing the path of
  // the choice - Europe › France › Paris - that opens the tree as the WAI-ARIA tree view:
  // the arrow keys up and down the open items, Right opening an item or going to its first
  // child, Left closing it or going to its parent, Home and End, a letter typed going to the
  // next item it starts, * opening every item beside the one with the focus, Enter or Space
  // choosing, Escape closing. It posts the value of the item chosen. The items are the paths
  // of options= - 'Europe > France > Paris, Asia > Japan', each step an item under the one
  // before it, its text its value - or the rows of the store data= names - flat, each naming
  // its parent= field (parent unless given, empty for a root), or nested, its children in
  // the field children= - the value of a row its key= field, its id unless given, the text
  // its field=, else its name, label or title; or the lines between the tags, a line indented
  // deeper than the one before it its child, its text its value. A value names one item: a
  // second one, a row under a parent that is none and a cycle of parents are named under the
  // strict check, and left out without it. leaves lets only an item without children be
  // chosen; value= the item chosen before a post, placeholder= what the button says before a
  // choice. After a post it shows the item posted - none when the form came back without
  // one, and a posted value that is no item is not shown: rules='required|in:...' holds it
  // to the items.

  include_once PAD . 'subs/inputs/_lib/tree.php';

  $padInputKey    = (string) padInputTake ( 'key', '' );
  $padInputParent = (string) padInputTake ( 'parent', 'parent' );
  $padInputField  = (string) padInputTake ( 'field', '' );
  $padInputKids   = (string) padInputTake ( 'children', 'children' );
  $padInputLeaves = padInputFlag ( 'leaves' );
  $padInputChoose = padInputTake ( 'placeholder', 'Choose' );

  $padInputPaths = padInputTake ( 'options', NULL );
  $padInputRows  = ( $padInputPaths === NULL ) ? padInputRows () : NULL;

  if ( $padInputPaths !== NULL )
    $padInputNodes = padInputTreePaths ( $padInputPaths );
  elseif ( $padInputRows !== NULL )
    $padInputNodes = padInputTreeRows ( $padInputRows, $padInputKey, $padInputParent, $padInputField, $padInputKids );
  else
    $padInputNodes = padInputTreeLines ( padInputSource () );

  if ( ! $padInputNodes and $GLOBALS ['padCheckSyntax'] )
    padError ( "the " . padInputWhat () . " has no items - options='Europe > France, Asia > Japan', data= or a line each between its tags, a child indented under its parent" );

  // A form that came back without the field had nothing chosen in it - a radio group with no
  // choice is left out of the post - so value= does not come back in its place.

  $padInputNow ['value'] = padInputValue ( '' );

  if ( ( $padInputSource = padFormSource () ) !== NULL and ! padFormFind ( $padInputSource, padInputName () ) [0] )
    $padInputNow ['value'] = '';

  return padInputField ( function ( $head, $rest, $label, $error, $id, $worded ) use ( $padInputNodes, $padInputLeaves, $padInputChoose ) {

    global $padInputNow;

    $text = preg_match ( '/^<label[^>]*>(.*)<\/label>$/s', $label, $own ) ? $own [1] : padInputEscape ( $worded );

    // The label= is the legend: a radio group is named by its fieldset, not by a <label> of
    // one of its radios, and the aria-label a field without a label gets is the legend too.

    $rest  = preg_replace ( '/\s+aria-label="[^"]*"/', '', $rest );
    $class = padInputOwn ( $rest, [ 'class' => 'pi-tree-input' ] );
    $count = 0;
    $path  = [];
    $list  = padInputTreeList ( $padInputNodes, padInputEscape ( padInputName () ), $id, $padInputNow ['value'], $rest, $class, $padInputLeaves, $count, $path );

    $legend = padInputEscape ( "$id-legend" );
    $choose = is_scalar ( $padInputChoose ) ? (string) $padInputChoose : 'Choose';

    return padInputFrame ( 'treepicker', $worded,
             "<legend id=\"$legend\">$text</legend>"
           . '<div class="pi-anchor"><button type="button" class="pi-button pi-tree-open" aria-haspopup="tree" aria-expanded="false" hidden>'
           . '<span class="pi-tree-path">' . padInputEscape ( $path ? implode ( ' › ', $path ) : $choose ) . '</span></button></div>'
           . "<ul class=\"pi-tree-list\">$list</ul>",
             $error, [ 'choose' => $choose, 'leaves' => $padInputLeaves ], 'fieldset' );

  } );

?>
