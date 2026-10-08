<?php

  // Tabs without JavaScript - the {tabs} tag, and the {tab} items that {accordion} and
  // {carousel} take as well.
  //
  //   {tabs active='Specs'}
  //     {tab 'Overview'}<p>A kettle that ...</p>{/tab}
  //     {tab 'Specs'}<table>...</table>{/tab}
  //   {/tabs}
  //
  // A tab is a radio button and its label, the panels behind them: the CSS shows the panel
  // whose radio is checked. The browser gives the group what a tab list needs - one stop in
  // the tab order, the arrow keys moving between the tabs, the name and 'checked' read out -
  // so there is no script at all, and no fragment in the address either: a page with two
  // tab sets keeps both choices, and the back button keeps its meaning. Printed, every panel
  // shows.
  //
  // The items are collected while the content renders: {tabs} (and {accordion}, {carousel})
  // asks for the end walk, opens a collection under its level, and every {tab} below it -
  // also one made in a loop, {specs}{tab $name}...{/tab}{/specs} - renders its content and
  // hands it over instead of printing it. At the end walk the owner builds its markup from
  // what was handed over.
  //
  // padTabsOpen    opens the collection of an owner's level
  // padTabsOwner   the level of the nearest owner below a {tab}, FALSE outside one
  // padTabsAdd     an item handed to its owner
  // padTabsTake    the owner's items, the collection closed
  // padTabsItems   the items checked: a label where the owner needs one, nothing else in
  //                between them, at least one
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
  // shows, and the strict check says so.

  function padTabsItems ( $kind, $rest ) {

    global $padCheckSyntax;

    $items = padTabsTake ();

    if ( $padCheckSyntax and trim ( $rest ) !== '' )
      padError ( "what stands between the {tab}s of a {" . $kind . "} never renders - put it inside a {tab}" );

    if ( ! $items and $padCheckSyntax )
      padError ( "the {" . $kind . "} has no {tab} - {" . $kind . "}{tab 'One'}...{/tab}{/" . $kind . "}" );

    return $items;

  }

  // An item named by its number, counted from 1, or by its label. 0 is none of them.

  function padTabsPick ( $items, $pick, $kind, $option ) {

    global $padCheckSyntax;

    if ( $pick === '' or $pick === NULL )
      return 0;

    if ( is_numeric ( $pick ) and (int) $pick >= 1 and (int) $pick <= count ( $items ) and (int) $pick == $pick )
      return (int) $pick;

    foreach ( $items as $index => $item )
      if ( (string) $item ['label'] === (string) $pick )
        return $index + 1;

    if ( $padCheckSyntax )
      padError ( "the {" . $kind . "} has no tab '" . padMakeSafe ( (string) $pick, 40 ) . "' for $option= - a number from 1 to "
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

  // The rules once per page, for twelve tabs a set; a set of more adds the rules of its
  // further tabs, each once.

  function padTabsStyle ( $count ) {

    $slots = '';

    for ( $n = 1; $n <= 12; $n++ )
      $slots .= padTabsSlot ( $n );

    $css = padWidgetStyle ( 'tabs',
      [ 'accent' => [ '#2a78d6', '#5598e7' ],
        'text'   => [ '#1f1f1d', '#ecebe6' ],
        'muted'  => [ '#62615c', '#a9a8a0' ],
        'line'   => [ '#e4e3df', '#3a3a37' ] ],
      '.pad-tabs{display:flex;flex-wrap:wrap;align-items:flex-end;margin:0 0 1em}'
      . '.pad-tabs-radio{position:absolute;opacity:0;width:1px;height:1px;margin:0;pointer-events:none}'
      . '.pad-tabs-label{position:relative;z-index:1;margin-bottom:-1px;padding:.55em 1em;border-bottom:2px solid transparent;'
      .   'color:var(--pad-tabs-muted);font-weight:500;cursor:pointer;user-select:none}'
      . '.pad-tabs-label:hover{color:var(--pad-tabs-text)}'
      . '.pad-tabs-radio:checked+.pad-tabs-label{color:var(--pad-tabs-text);border-bottom-color:var(--pad-tabs-accent)}'
      . '.pad-tabs-radio:focus-visible+.pad-tabs-label{outline:2px solid var(--pad-tabs-accent);outline-offset:-2px;border-radius:6px 6px 0 0}'
      . '.pad-tabs-panel{display:none;flex:1 0 100%;order:1;padding:1em 0 0;border-top:1px solid var(--pad-tabs-line)}'
      . $slots
      . '@media print{.pad-tabs-panel{display:block}}' );

    for ( $n = 13; $n <= $count; $n++ )
      if ( padWidgetOnce ( "tabs-slot:$n" ) )
        $css .= padProtect ( '<style>' . padTabsSlot ( $n ) . '</style>' );

    return $css;

  }

  function padTabsSlot ( $n ) {

    return ".pad-tabs>.pad-tabs-radio:nth-of-type($n):checked~.pad-tabs-panel:nth-of-type($n){display:block}";

  }

?>
