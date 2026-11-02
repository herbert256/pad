<?php

  // Tabs without JavaScript - the tabs widget, and the {pad:tab} items that {widget:accordion},
  // {widget:carousel}, {widget:wizard} and {widget:lightbox} take as well.
  //
  //   {widget:tabs active='Specs'}
  //     {pad:tab 'Overview'}<p>A kettle that ...</p>{/pad:tab}
  //     {pad:tab 'Specs'}<table>...</table>{/pad:tab}
  //   {/widget:tabs}
  //
  // A tab is a radio button and its label, the panels behind them: the CSS shows the panel
  // whose radio is checked. The browser gives the group what a tab list needs - one stop in
  // the tab order, the arrow keys moving between the tabs, the name and 'checked' read out -
  // so there is no script at all, and no fragment in the address either: a page with two
  // tab sets keeps both choices, and the back button keeps its meaning. Printed, every panel
  // shows.
  //
  // The items are collected while the content renders: {widget:tabs} (and the other owners) asks
  // for the end walk, opens a collection under its level, and every {pad:tab} below it -
  // also one made in a loop, {specs}{pad:tab $name}...{/pad:tab}{/specs} - renders its content and
  // hands it over instead of printing it. At the end walk the owner builds its markup from
  // what was handed over.
  //
  // padTabsOpen    opens the collection of an owner's level
  // padTabsOwner   the level of the nearest owner below a {pad:tab}, FALSE outside one
  // padTabsAdd     an item handed to its owner
  // padTabsTake    the owner's items, the collection closed
  // padTabsItems   the items checked: a label where the owner needs one, nothing else in
  //                between them, at least one - or none, for an owner that allows it
  // padTabsPick    which item an active= or open= names - a number from 1, or a label
  // padTabs        the markup of a tab set

  function &padTabsStore () {

    static $store = [];

    return $store;

  }

  function padTabsOpen ( $kind ) {

    global $pad, $padLevelId;

    $store = &padTabsStore ();

    $store [ $padLevelId [$pad] ] = [ 'kind' => $kind, 'items' => [] ];

  }

  function padTabsOwner () {

    global $pad, $padLevelId;

    $store = &padTabsStore ();

    for ( $i = $pad - 1; $i >= 0; $i-- )
      if ( isset ( $store [ $padLevelId [$i] ?? 0 ] ) )
        return $i;

    return FALSE;

  }

  function padTabsKind ( $owner ) {

    global $padLevelId;

    $store = &padTabsStore ();

    return $store [ $padLevelId [$owner] ] ['kind'];

  }

  function padTabsAdd ( $owner, $label, $content ) {

    global $padLevelId;

    $store = &padTabsStore ();

    $store [ $padLevelId [$owner] ] ['items'] [] = [ 'label' => $label, 'content' => $content ];

  }

  function padTabsTake () {

    global $pad, $padLevelId;

    $store = &padTabsStore ();
    $items = $store [ $padLevelId [$pad] ] ['items'] ?? [];

    unset ( $store [ $padLevelId [$pad] ] );

    return $items;

  }

  // What the owner rendered itself is only the room between its items: text there never
  // shows, and the strict check says so. An owner that may have no item - a {widget:sortable},
  // whose items a loop over the application's rows makes - asks with $none: without an
  // item it gets none and shows what its content rendered, nothing or the loop's @else@.

  function padTabsItems ( $kind, $rest, $none = FALSE ) {

    global $padCheckSyntax;

    $items = padTabsTake ();

    if ( ! $items and $none )
      return [];

    if ( $padCheckSyntax and trim ( $rest ) !== '' )
      padError ( "what stands between the {pad:tab}s of a {" . "widget:$kind" . "} never renders - put it inside a {pad:tab}" );

    if ( ! $items and $padCheckSyntax )
      padError ( "the {" . "widget:$kind" . "} has no {pad:tab} - {" . "widget:$kind" . "}{pad:tab 'One'}...{/pad:tab}{/" . "widget:$kind" . "}" );

    return $items;

  }

  // An item named by its number, counted from 1, or by its label. 0 is none of them.

  function padTabsPick ( $items, $pick, $kind, $option ) {

    global $padCheckSyntax;

    if ( $pick === '' or $pick === NULL )
      return 0;

    // A label is read as the characters it holds, as the {pad:tab} reads its own: a field
    // inside the quoted active= or open= meets the tab named by the same field.

    if ( is_string ( $pick ) )
      $pick = padParmText ( $pick );

    if ( is_numeric ( $pick ) and (int) $pick >= 1 and (int) $pick <= count ( $items ) and (int) $pick == $pick )
      return (int) $pick;

    foreach ( $items as $index => $item )
      if ( (string) $item ['label'] === (string) $pick )
        return $index + 1;

    if ( $padCheckSyntax )
      padError ( "the {" . "widget:$kind" . "} has no tab '" . padMakeSafe ( (string) $pick, 40 ) . "' for $option= - a number from 1 to "
                 . count ( $items ) . ' or the label of one' );

    return 0;

  }

  function padTabs ( $items, $active ) {

    $id    = padWidgetId ( 'tabs', array_column ( $items, 'label' ) );
    $count = count ( $items );
    $tabs  = $panels = '';

    if ( $active < 1 )
      $active = 1;

    foreach ( $items as $index => $item ) {

      $n     = $index + 1;
      $check = ( $n == $active ) ? ' checked' : '';

      $tabs .= padProtect ( "<input class=\"pad-tabs-radio\" type=\"radio\" name=\"$id\" id=\"$id-$n\" aria-controls=\"$id-$n-panel\"$check>"
                          . "<label class=\"pad-tabs-label\" for=\"$id-$n\" id=\"$id-$n-label\">" . padWidgetAttr ( $item ['label'] ) . '</label>' );

      $panels .= padProtect ( "<div class=\"pad-tabs-panel\" id=\"$id-$n-panel\" role=\"region\" aria-labelledby=\"$id-$n-label\">" )
               . $item ['content']
               . '</div>';

    }

    return padTabsStyle ( $count )
         . padProtect ( "<div class=\"pad-tabs\" id=\"$id\">" )
         . $tabs . $panels
         . '</div>';

  }

  // The rules once per page - www/assets/widgets/tabs.css, which holds the slots of twelve tabs a set; a
  // set of more adds the rules of its further tabs, each once.

  function padTabsStyle ( $count ) {

    $css = padWidgetStyle ( 'tabs',
      [ 'accent' => [ '#2a78d6', '#5598e7' ],
        'text'   => [ '#1f1f1d', '#ecebe6' ],
        'muted'  => [ '#62615c', '#a9a8a0' ],
        'line'   => [ '#e4e3df', '#3a3a37' ] ],
      padWidgetAsset ( 'tabs.css' ) );

    for ( $n = 13; $n <= $count; $n++ )
      if ( padWidgetOnce ( "tabs-slot:$n" ) )
        $css .= padProtect ( '<style' . padStyleNonce () . '>' . padTabsSlot ( $n ) . '</style>' );

    return $css;

  }

  function padTabsSlot ( $n ) {

    return ".pad-tabs>.pad-tabs-radio:nth-of-type($n):checked~.pad-tabs-panel:nth-of-type($n){display:block}";

  }

?>
