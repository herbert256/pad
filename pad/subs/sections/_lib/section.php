<?php

  // The sections subsystem: the blocks a landing page is built of - a hero, the features, the
  // plans and their prices, what customers say, the questions, a call to action, the numbers,
  // the steps, the team, the logos, a newsletter form, the contact details, a gallery and the
  // footer.
  //
  //   {section:hero title='Ship pages, not plumbing', button='Start', link='?start'}
  //     Templates drive the page: no routes, no controllers.
  //     {pad:slot 'eyebrow'}New in 2026{/pad:slot}
  //   {/section:hero}
  //
  //   {section:pricing title='Plans'}
  //     {section:item 'Starter', price='€0', period='month'}3 projects{/section:item}
  //     {section:item 'Pro', price='€12', period='month', featured}Every project{/section:item}
  //   {/section:pricing}
  //
  //   {section:faq title='Questions', data='questions'/}
  //
  // A section is a file of subs/sections/types/ - the markup of one kind - and the files there
  // are the sections there are (padSectionTypes, padKinds). Its style is www/assets/sections/section.css,
  // shared, and www/assets/sections/<kind>.css, each written once per page with the CSP nonce: pure CSS, no
  // script. The colours are the --pad-section-* custom properties, whose defaults are the
  // frame's palette (--bg, --text, --accent ... of www/_common/pad.css), so a page in the frame
  // and a theme colour the sections without a word; a page without the frame gets light and
  // dark steps of its own (light-dark(), padWidgetColors). Every section is a size container,
  // its layout following the room it has (@container), not the window.
  //
  // A section's parts come three ways, the first that has one winning: a {pad:slot 'name'} pair
  // standing directly in its content, an option of the part's name, and - for the text - the
  // rest of the content. The slots are taken out of the content before it renders, wrapped in
  // sealed markers and put back, so they render where the content renders, in its scope - a
  // field of the loop around the section, a tag - and come apart again at the end walk
  // (padSectionOpen, padSectionSplit). A list section - features, pricing, faq ... - takes its
  // items from the rows data= names, and from {section:item} pairs inside it, which render
  // with the content and hand themselves to the nearest section below them (padSectionOwner),
  // as a {widget:tab} hands itself to its {widget:tabs}.
  //
  // The kind's file runs in padSectionBuild, which knows the section being built
  // ($padSectionNow); the functions it calls are these:
  //
  //   padSectionParts    the parts a kind has - a slot of another name is reported
  //   padSectionPart     a part's markup: its slot, its option escaped, the content for text
  //   padSectionHead     the eyebrow, the heading and the text of a section
  //   padSectionActions  the buttons: the actions slot, or button= link= second= secondLink=
  //   padSectionImage    the image slot, or image= with alt=
  //   padSectionItems    the items of a list section, from rows and from {section:item} pairs
  //   padSectionField    an item's field as markup, escaped where it was a value
  //   padSectionRaw      an item's field as it was given
  //   padSectionForm     a form that posts back, the way {input:form} writes one
  //   padSection         the section around what the kind built, its style once per page

  // A link a section writes is judged as a widget's is (padWidgetLink): the widgets' library
  // is the one place that knows which schemes a browser runs.

  include_once PAD . 'subs/widgets/_lib/widget.php';

  function padSectionTypes () {

    return padKinds ( 'sections' );

  }

  function padSectionType ( $name ) {

    return padKind ( 'sections', $name );

  }

  // The sections being built or collected, by the level that opened them.

  function &padSectionStore () {

    static $store = [];

    return $store;

  }

  // The tag the section is written as, for a message: {section:hero}, {hero} never (sections
  // are not reached bare), or {section:hero}.

  function padSectionWhat ( $kind = NULL ) {

    global $pad, $padType;

    $kind = $kind ?? ( $GLOBALS ['padSectionNow'] ['kind'] ?? '' );

    return ( ( $padType [$pad] ?? '' ) == 'pad' ) ? "{section:$kind}" : "{section:$kind}";

  }

  // The section's first parameter, such as the heading in {section:cta 'Ready?'}, or NULL.

  function padSectionValue () {

    global $pad, $padType, $padOpt;

    $value = $padOpt [$pad] [ ( ( $padType [$pad] ?? '' ) == 'pad' ) ? 2 : 1 ] ?? NULL;

    if ( $value === NULL or is_array ( $value ) )
      return NULL;

    return trim ( padParmText ( (string) $value ) );

  }

  // The start walk of a section: its rows, an empty collection for its items, and its slots
  // taken out of the content and put back between markers, so they render with the rest.
  // $content is the level's content, the template between the tags.

  function padSectionOpen ( $kind, &$content, $pair, $rows ) {

    global $pad, $padLevelId;

    static $opened = 0;

    $store = &padSectionStore ();
    $state = [ 'kind' => $kind, 'rows' => $rows, 'items' => [], 'fills' => [], 'seal' => '',
               'written' => $pair and padSectionWritesItems ( $content ) ];

    if ( $pair and is_string ( $content ) ) {

      $fills = padSlotTake ( $content );

      if ( $fills ) {
        $state ['seal'] = 'section ' . ( ++$opened ) . ' ' . ( $padLevelId [$pad] ?? 0 );
        foreach ( array_keys ( $fills ) as $n => $name ) {
          $state ['fills'] [$n] = (string) $name;
          $content .= padSectionMark ( $state ['seal'], $n, 'open' ) . $fills [$name] . padSectionMark ( $state ['seal'], $n, 'close' );
        }
      }

    }

    $store [ $padLevelId [$pad] ?? 0 ] = $state;

  }

  // A marker around a slot in the content: private-use characters and a keyed seal of the
  // section and the slot (padMarkSeal), so a value that came in with the request is never
  // taken for one.

  function padSectionMark ( $seal, $n, $end ) {

    return "\u{E0E0}" . padMarkSeal ( "$seal $n $end" ) . "\u{E0E1}";

  }

  // The rows data= names - a {format:auto} store, a sequence store, the page's array, a field
  // holding a list or a _data file, as {chart:bar} and the widgets read them - or NULL without
  // data=. The level got those rows as its data from the data option, and would write the
  // section once per row: it gets its one default occurrence back.

  function padSectionRows ( $kind ) {

    global $pad, $padPrm, $padData, $padDataStore, $pqStore, $padCheckSyntax;

    if ( ! isset ( $padPrm [$pad] ['data'] ) )
      return NULL;

    padTagParm ( 'data' );

    $rows = $padData [$pad];
    $name = $padPrm [$pad] ['data'];

    $padData [$pad] = padDefaultData ();

    if ( ! is_string ( $name ) or ! padValidVar ( $name ) )
      return $rows;

    if ( isset ( $padDataStore [$name] ) ) return $padDataStore [$name];
    if ( isset ( $pqStore      [$name] ) ) return $pqStore      [$name];

    if ( isset ( $GLOBALS [$name] ) and is_array ( $GLOBALS [$name] ) )
      return $GLOBALS [$name];

    if ( padFieldCheck ( $name ) and is_array ( $field = padFieldValue ( $name ) ) )
      return $field;

    if ( $file = padDataFileName ( $name ) )
      return padDataFileData ( $file );

    if ( $padCheckSyntax and ! padStrHidden ( $name ) )
      padError ( "there is no data named '$name' for the " . padSectionWhat ( $kind ) );

    return [];

  }

  // Whether the template between a section's tags writes items - a {section:item} or a
  // {section:item}, in a loop or not - whatever they render to.

  function padSectionWritesItems ( $content ) {

    return is_string ( $content ) and preg_match ( '/\{~?\s*section(?:\s*:\s*item\b|\s+[\'"]item[\'"])/i', $content );

  }

  // Whether a kind lists items - its file reads them (padSectionItems).

  function padSectionLists ( $kind ) {

    return str_contains ( (string) padFileGet ( PAD . "subs/sections/types/$kind.php" ), 'padSectionItems (' );

  }

  // Whether the section has a handling option - sort=, first=, where= ... - which works on
  // the rows of data= (section.php), as on any data tag's.

  function padSectionHandles () {

    global $pad, $padParms;

    foreach ( $padParms [$pad] ?? [] as $one )
      if ( $one ['padPrmKind'] == 'option' and is_string ( $one ['padPrmName'] ) and padFileExists ( PAD . "handling/types/{$one ['padPrmName']}.php" )
           and ! in_array ( $one ['padPrmName'], [ 'negative', 'orderly', 'duplicates', 'atLeastOnce', 'left', 'right', 'both', 'sum', 'avg', 'min', 'max' ] ) )
        return TRUE;

    return FALSE;

  }

  // The section a {section:item} belongs to: the level of the nearest section below it, or
  // FALSE outside one.

  function padSectionOwner () {

    global $pad, $padLevelId;

    $store = &padSectionStore ();

    for ( $i = $pad - 1; $i >= 0; $i-- )
      if ( isset ( $store [ $padLevelId [$i] ?? -1 ] ) )
        return $i;

    return FALSE;

  }

  // An item handed to its section: its first value, its options as they were given and the
  // markup its content rendered to (NULL for a single {section:item .../}). The options that
  // are the engine's own - data=, sort=, the handling - are left to the engine.

  function padSectionItemAdd ( $owner, $content ) {

    global $pad, $padPrm, $padLevelId;

    $options = [];

    foreach ( array_keys ( $padPrm [$pad] ?? [] ) as $name ) {

      $name = (string) $name;

      if ( padFileExists ( PAD . "options/$name.php" ) or padFileExists ( PAD . "handling/types/$name.php" ) )
        continue;

      $value = padTagParm ( $name );

      $options [$name] = is_string ( $value ) ? trim ( padParmText ( $value ) ) : $value;

    }

    $store = &padSectionStore ();

    $store [ $padLevelId [$owner] ] ['items'] [] = [
      'first'   => padSectionValue (),
      'options' => $options,
      'content' => ( $content === NULL ) ? NULL : trim ( (string) $content )
    ];

  }

  // The end walk of a section - or its only walk, as a single tag: the slots split off what
  // the content rendered to, the rest its text, the items and rows it collected - and the
  // kind's file run to build it.

  function padSectionBuild ( $kind, $content ) {

    global $pad, $padLevelId, $padSectionNow;

    $store = &padSectionStore ();
    $key   = $padLevelId [$pad] ?? 0;
    $state = $store [$key] ?? [ 'kind' => $kind, 'rows' => NULL, 'items' => [], 'fills' => [], 'seal' => '', 'written' => FALSE ];

    unset ( $store [$key] );

    [ $fills, $body ] = padSectionSplit ( $state, (string) $content );

    $before        = $padSectionNow ?? NULL;
    $padSectionNow = [ 'kind'  => $kind,  'fills' => $fills, 'body'  => $body, 'rows' => $state ['rows'],
                       'items' => $state ['items'], 'written' => $state ['written'], 'parts' => NULL, 'held' => [], 'listed' => FALSE ];

    $html = padSectionKind ( $kind );

    $padSectionNow = $before;

    return $html;

  }

  function padSectionKind ( $kind ) {

    return include PAD . "subs/sections/types/$kind.php";

  }

  // The slots out of the rendered content, by the markers padSectionOpen put round them. A
  // slot whose markers did not come through - a pipe on the tag that changed the content - is
  // an empty one. [ fills by name, the rest trimmed ].

  function padSectionSplit ( $state, $content ) {

    $fills = [];

    foreach ( $state ['fills'] as $n => $name ) {

      $open  = padSectionMark ( $state ['seal'], $n, 'open' );
      $close = padSectionMark ( $state ['seal'], $n, 'close' );
      $from  = strpos ( $content, $open );
      $to    = ( $from === FALSE ) ? FALSE : strpos ( $content, $close, $from );

      if ( $from === FALSE or $to === FALSE ) {
        $fills [$name] = '';
        continue;
      }

      $fills [$name] = trim ( substr ( $content, $from + strlen ( $open ), $to - $from - strlen ( $open ) ) );
      $content       = substr ( $content, 0, $from ) . substr ( $content, $to + strlen ( $close ) );

    }

    return [ $fills, trim ( $content ) ];

  }

  // The parts a kind has: a slot of a name that is not one of them is reported.

  function padSectionParts ( $parts ) {

    global $padSectionNow, $padCheckSyntax;

    $padSectionNow ['parts'] = $parts;

    if ( $padCheckSyntax )
      foreach ( array_keys ( $padSectionNow ['fills'] ) as $name )
        if ( ! in_array ( $name, $parts, TRUE ) )
          padError ( "the " . padSectionWhat () . " has no part '" . padMakeSafe ( $name, 20 ) . "' to fill - " . implode ( ', ', $parts ) );

  }

  // Markup kept out of the protection padSection gives the section's own: what a slot, the
  // content or an item rendered to is the page's already, and comes back in its place.

  function padSectionHold ( $html ) {

    global $padSectionNow;

    $n = count ( $padSectionNow ['held'] );

    $padSectionNow ['held'] [$n] = (string) $html;

    return "\u{E0E2}$n\u{E0E3}";

  }

  // A text option as the characters it holds, trimmed; '' when not given.

  function padSectionOption ( $option, $default = '' ) {

    $value = padTagParm ( $option, $default );

    if ( is_bool ( $value ) or is_array ( $value ) or is_object ( $value ) or $value === NULL )
      return $default;

    return trim ( padParmText ( (string) $value ) );

  }

  function padSectionEscape ( $text ) {

    return padWidgetAttr ( (string) $text );

  }

  // A bare option given or not - featured, form.

  function padSectionFlag ( $option ) {

    $value = padTagParm ( $option, FALSE );

    return padSectionTrue ( $value );

  }

  function padSectionTrue ( $value ) {

    if ( is_string ( $value ) )
      return ! in_array ( strtolower ( trim ( $value ) ), [ '', '0', 'false', 'no', 'off' ], TRUE );

    return (bool) $value;

  }

  // An option that is one of a few words, named under the strict check when it is another.

  function padSectionChoice ( $option, $default, $choices ) {

    global $padCheckSyntax;

    $value = padTagParm ( $option, $default );
    $value = is_scalar ( $value ) ? strtolower ( trim ( padParmText ( (string) $value ) ) ) : '';

    if ( in_array ( $value, $choices, TRUE ) )
      return $value;

    if ( $padCheckSyntax )
      padError ( "the " . padSectionWhat () . " has no $option '" . padMakeSafe ( $value, 20 ) . "' - " . implode ( ', ', $choices ) );

    return $default;

  }

  // A whole number option held to its bounds, named under the strict check when it is not.

  function padSectionInt ( $option, $default, $min, $max ) {

    global $padCheckSyntax;

    $value = padTagParm ( $option, $default );

    if ( is_string ( $value ) )
      $value = trim ( padParmText ( $value ) );

    if ( ( is_int ( $value ) or ( is_string ( $value ) and preg_match ( '/^\d+$/D', $value ) ) )
         and (int) $value >= $min and (int) $value <= $max )
      return (int) $value;

    if ( $padCheckSyntax )
      padError ( "the " . padSectionWhat () . " has no $option '" . padMakeSafe ( is_scalar ( $value ) ? (string) $value : '', 20 ) . "' - a whole number from $min to $max" );

    return $default;

  }

  // A link or the address of a picture: escaped for its attribute, and '' for one of another
  // scheme than http, https or mailto - javascript:, data: - which a row of data may hold.

  function padSectionLink ( $link ) {

    return is_scalar ( $link ) ? padWidgetLink ( (string) $link ) : '';

  }

  // The markup of a part: its slot when one was given - a slot written empty is an empty
  // part - else, for the text, what the content rendered to, else its option, escaped. The
  // heading's option is title=, or the section's first value. '' when there is none.

  function padSectionPart ( $part, $option = NULL ) {

    global $padSectionNow, $padCheckSyntax;

    $option = $option ?? $part;

    if ( $part == 'title' ) {
      $first = padSectionValue ();
      $given = padSectionOption ( 'title' );
      if ( $first !== NULL and $first !== '' and $given !== '' and $padCheckSyntax )
        padError ( "the " . padSectionWhat () . " has its heading twice - as its first value and as title=" );
    }

    $value = padSectionOption ( $option );

    if ( $part == 'title' and $value === '' )
      $value = (string) padSectionValue ();

    if ( array_key_exists ( $part, $padSectionNow ['fills'] ) )
      return padSectionHold ( $padSectionNow ['fills'] [$part] );

    if ( $part == 'text' and $padSectionNow ['body'] !== '' )
      return padSectionHold ( $padSectionNow ['body'] );

    return padSectionEscape ( $value );

  }

  // Whether a part's markup holds blocks of its own - paragraphs, a list - so it is put in a
  // <div> rather than a <p>.

  function padSectionBlock ( $html ) {

    global $padSectionNow;

    $html = preg_replace_callback ( '/\x{E0E2}(\d+)\x{E0E3}/u', fn ( $m ) => $padSectionNow ['held'] [ (int) $m [1] ] ?? '', $html );

    return (bool) preg_match ( '/<(p|div|ul|ol|dl|h[1-6]|table|figure|blockquote|section|article|form|pre|details|address)\b/i', $html );

  }

  // A text in a paragraph of its class, or in a <div> when it holds blocks of its own.

  function padSectionTextBlock ( $html, $class ) {

    return padSectionBlock ( $html ) ? "<div class=\"$class\">$html</div>" : "<p class=\"$class\">$html</p>";

  }

  // An icon of {icon:star} by its name (subs/icons/), decoration beside the text it goes with; ''
  // without a name, and a name that is no icon is reported with the names close to it.

  function padSectionIcon ( $name ) {

    global $padCheckSyntax;

    $name = is_scalar ( $name ) ? strtolower ( trim ( (string) $name ) ) : '';

    if ( $name === '' )
      return '';

    include_once PAD . 'subs/icons/_lib/icon.php';

    if ( ! padKind ( 'icons', $name ) ) {
      if ( $padCheckSyntax ) {
        $near = padIconSimilar ( $name );
        padError ( "the " . padSectionWhat () . " has no icon '" . padMakeSafe ( $name, 20 ) . "'" . ( $near ? ' - ' . implode ( ', ', $near ) : '' ) );
      }
      return '';
    }

    return padIcon ( $name, 22 );

  }

  // The heading level of the section (level=, 2 unless given) and its items' (one more).

  function padSectionLevel () {

    global $padSectionNow;

    return $padSectionNow ['level'] ??= padSectionInt ( 'level', 2, 1, 6 );

  }

  // The id the section's parts are named from - its id= when given - made from what it shows,
  // so the same section is the same markup on every request.

  function padSectionId () {

    global $padSectionNow, $pad, $padPrm, $padCheckSyntax;

    if ( isset ( $padSectionNow ['id'] ) )
      return $padSectionNow ['id'];

    $given = padSectionOption ( 'id' );

    if ( $given !== '' and ! preg_match ( '/^[A-Za-z][A-Za-z0-9_-]{0,63}$/D', $given ) ) {
      if ( $padCheckSyntax )
        padError ( "the " . padSectionWhat () . " has no id '" . padMakeSafe ( $given, 20 ) . "' - letters, digits, _ and -, starting with a letter" );
      $given = '';
    }

    $seed = [ $padSectionNow ['kind'], $padSectionNow ['fills'], $padSectionNow ['body'], $padSectionNow ['items'],
              array_map ( fn ( $one ) => is_scalar ( $one ) ? (string) $one : '', $padPrm [$pad] ?? [] ) ];

    $padSectionNow ['given'] = $given;

    return $padSectionNow ['id'] = ( $given !== '' ) ? $given : padWidgetId ( 'section-' . $padSectionNow ['kind'], $seed );

  }

  // The head of a section: the small line above the heading (eyebrow), the heading and the
  // text under it, each when there is one. $parts says which of them the kind has.

  function padSectionHead ( $parts = [ 'eyebrow', 'title', 'text' ], $class = 'ps-head' ) {

    global $padSectionNow;

    $id    = padSectionId ();
    $level = padSectionLevel ();
    $html  = '';

    if ( in_array ( 'eyebrow', $parts ) and ( $eyebrow = padSectionPart ( 'eyebrow' ) ) !== '' )
      $html .= "<p class=\"ps-eyebrow\">$eyebrow</p>";

    if ( in_array ( 'title', $parts ) and ( $title = padSectionPart ( 'title' ) ) !== '' ) {
      $html .= "<h$level class=\"ps-title\" id=\"$id-title\">$title</h$level>";
      $padSectionNow ['titled'] = TRUE;
    }

    if ( in_array ( 'text', $parts ) and ( $text = padSectionPart ( 'text' ) ) !== '' )
      $html .= padSectionBlock ( $text ) ? "<div class=\"ps-text\">$text</div>" : "<p class=\"ps-text\">$text</p>";

    return ( $html === '' ) ? '' : "<div class=\"$class\">$html</div>";

  }

  // The buttons: the actions slot as it was written, else a button for button= and link=,
  // and a quieter one for second= and secondLink=.

  function padSectionActions () {

    global $padSectionNow, $padCheckSyntax;

    $buttons = [ [ 'button', 'link', 'ps-button' ], [ 'second', 'secondLink', 'ps-button ps-button-second' ] ];
    $html    = '';

    foreach ( $buttons as [ $text, $link, $class ] ) {

      $label = padSectionOption ( $text );
      $href  = padSectionOption ( $link );

      if ( $label === '' ) {
        if ( $href !== '' and $padCheckSyntax )
          padError ( "the " . padSectionWhat () . " has a $link= without its $text=" );
        continue;
      }

      if ( $href === '' and $padCheckSyntax )
        padError ( "the $text= of the " . padSectionWhat () . " needs its $link= - the page it goes to" );

      $href  = padSectionLink ( $href );
      $html .= "<a class=\"$class\"" . ( $href !== '' ? " href=\"$href\"" : '' ) . '>' . padSectionEscape ( $label ) . '</a>';

    }

    if ( array_key_exists ( 'actions', $padSectionNow ['fills'] ) )
      $html = padSectionHold ( $padSectionNow ['fills'] ['actions'] );

    return ( $html === '' ) ? '' : "<div class=\"ps-actions\">$html</div>";

  }

  // The picture of a section: the image slot, else image= with alt= (the alt text, '' a
  // picture that only decorates). $class is the figure's.

  function padSectionImage ( $class = 'ps-image' ) {

    global $padSectionNow;

    $src = padSectionOption ( 'image' );
    $alt = padSectionOption ( 'alt' );

    if ( array_key_exists ( 'image', $padSectionNow ['fills'] ) )
      return ( $padSectionNow ['fills'] ['image'] === '' ) ? '' : "<div class=\"$class\">" . padSectionHold ( $padSectionNow ['fills'] ['image'] ) . '</div>';

    $src = padSectionLink ( $src );

    if ( $src === '' )
      return '';

    return "<div class=\"$class\"><img src=\"$src\" alt=\"" . padSectionEscape ( $alt ) . '" loading="lazy"></div>';

  }

  // The items of a list section: the rows data= named, then the {section:item} pairs and
  // single tags inside it, each as [ 'raw' => fields as given, 'html' => fields that are
  // markup ]. $first is the field an item's first value fills, $fields every field the kind
  // reads, $content the field an item's content fills. A row may carry fields the kind does
  // not read; an item given an option the kind has no field for is reported. $what names the
  // items in a message: 'plans'.

  function padSectionItems ( $first, $fields, $content, $what ) {

    global $padSectionNow, $padCheckSyntax;

    $padSectionNow ['listed'] = TRUE;

    $items = [];

    foreach ( (array) ( $padSectionNow ['rows'] ?? [] ) as $row ) {

      if ( is_object ( $row ) )
        $row = padToArray ( $row );

      if ( ! is_array ( $row ) )
        $row = [ $first => $row ];

      $raw = [];

      foreach ( $fields as $field )
        if ( array_key_exists ( $field, $row ) and $row [$field] !== NULL )
          $raw [$field] = is_scalar ( $row [$field] ) ? (string) $row [$field] : $row [$field];

      $items [] = [ 'raw' => $raw, 'html' => [] ];

    }

    foreach ( $padSectionNow ['items'] as $item ) {

      $raw  = [];
      $html = [];

      foreach ( $item ['options'] as $name => $value ) {
        if ( ! in_array ( $name, $fields, TRUE ) ) {
          if ( $padCheckSyntax )
            padError ( "a {section:item} of the " . padSectionWhat () . " has no option '" . padMakeSafe ( $name, 20 ) . "' - " . implode ( ', ', array_diff ( $fields, [ $first ] ) ) );
          continue;
        }
        $raw [$name] = $value;
      }

      if ( $item ['first'] !== NULL and $item ['first'] !== '' ) {
        if ( isset ( $raw [$first] ) and $padCheckSyntax )
          padError ( "a {section:item} of the " . padSectionWhat () . " has its $first twice - as its first value and as $first=" );
        $raw [$first] ??= $item ['first'];
      }

      if ( $item ['content'] !== NULL and $item ['content'] !== '' )
        $html [$content] = $item ['content'];

      $items [] = [ 'raw' => $raw, 'html' => $html ];

    }

    // A section that has no items because its data= found no rows, or the items written
    // between its tags rendered none, is built without its list; one given neither is a
    // mistake of the template.

    if ( ! $items and $padCheckSyntax and $padSectionNow ['rows'] === NULL and ! ( $padSectionNow ['written'] ?? FALSE ) )
      padError ( "the " . padSectionWhat () . " has no $what - data='rows' or {section:item} pairs between its tags" );

    return $items;

  }

  // An item's field as markup: what its content rendered to, or its value escaped.

  function padSectionField ( $item, $field ) {

    if ( isset ( $item ['html'] [$field] ) )
      return padSectionHold ( $item ['html'] [$field] );

    $value = $item ['raw'] [$field] ?? '';

    return is_scalar ( $value ) ? padSectionEscape ( trim ( (string) $value ) ) : '';

  }

  // An item's field as plain text for an attribute - the alt of a picture from its caption:
  // what its content rendered to without its tags, or its value, escaped.

  function padSectionPlain ( $item, $field ) {

    $text = isset ( $item ['html'] [$field] )
          ? html_entity_decode ( strip_tags ( padUnprotect ( $item ['html'] [$field] ) ), ENT_QUOTES | ENT_HTML5, 'UTF-8' )
          : ( is_scalar ( $item ['raw'] [$field] ?? NULL ) ? (string) $item ['raw'] [$field] : '' );

    return padSectionEscape ( trim ( preg_replace ( '/\s+/u', ' ', $text ) ) );

  }

  function padSectionRaw ( $item, $field, $default = '' ) {

    return $item ['raw'] [$field] ?? $default;

  }

  function padSectionHas ( $item, $field ) {

    return isset ( $item ['html'] [$field] ) or ( isset ( $item ['raw'] [$field] ) and is_scalar ( $item ['raw'] [$field] ) and trim ( (string) $item ['raw'] [$field] ) !== '' );

  }

  // A list in one field: the items of an array, or the lines of a text - a ; between them
  // too - each escaped; a field that is markup (an item's content) is its lines, or its own
  // <li> elements as they stand.

  function padSectionList ( $item, $field ) {

    if ( isset ( $item ['html'] [$field] ) ) {

      $html = $item ['html'] [$field];

      if ( preg_match ( '/<li\b/i', $html ) )
        return [ preg_replace ( '/^\s*<ul\b[^>]*>|<\/ul>\s*$/i', '', $html ) ];

      $lines = [];
      foreach ( preg_split ( '/\R/', $html ) as $line )
        if ( trim ( $line ) !== '' )
          $lines [] = '<li>' . padSectionHold ( trim ( $line ) ) . '</li>';

      return $lines ? [ implode ( '', $lines ) ] : [];

    }

    $value = $item ['raw'] [$field] ?? [];
    $list  = is_array ( $value ) ? $value : preg_split ( '/\R|;/', (string) $value );
    $lines = '';

    foreach ( $list as $one )
      if ( is_scalar ( $one ) and trim ( (string) $one ) !== '' )
        $lines .= '<li>' . padSectionEscape ( trim ( (string) $one ) ) . '</li>';

    return ( $lines === '' ) ? [] : [ $lines ];

  }

  // The links of a footer's column from what its content rendered to, a line each: a line
  // with a bar is a label and its link - Pricing | ?pricing - a line of markup stands as it
  // is, another is text. The label is markup already; the link is read as a browser reads an
  // attribute and refused when it would run script (padWidgetLink).

  function padSectionFooterLinks ( $html ) {

    $list = '';

    foreach ( preg_split ( '/\R/', (string) $html ) as $line ) {

      $line = trim ( $line );

      if ( $line === '' )
        continue;

      $bar = str_contains ( $line, '<' ) ? FALSE : strrpos ( $line, '|' );

      if ( $bar === FALSE and ! str_contains ( $line, '<' ) )
        $bar = strrpos ( $line, "\u{E07C}" );

      if ( $bar === FALSE ) {
        $list .= '<li>' . padSectionHold ( $line ) . '</li>';
        continue;
      }

      $width = str_starts_with ( substr ( $line, $bar ), '|' ) ? 1 : strlen ( "\u{E07C}" );
      $label = trim ( substr ( $line, 0, $bar ) );
      $href  = padWidgetLink ( trim ( substr ( $line, $bar + $width ) ), TRUE );

      $list .= '<li>' . ( $href !== '' ? "<a href=\"$href\">" . padSectionHold ( $label ) . '</a>' : padSectionHold ( $label ) ) . '</li>';

    }

    return $list;

  }

  // The initials of a name, for a person without a picture: the first letters of the first
  // and the last word, upper case.

  function padSectionInitials ( $name ) {

    $words = preg_split ( '/\s+/u', trim ( (string) $name ), -1, PREG_SPLIT_NO_EMPTY );

    if ( ! $words )
      return '';

    $first = mb_substr ( $words [0], 0, 1 );
    $last  = ( count ( $words ) > 1 ) ? mb_substr ( end ( $words ), 0, 1 ) : '';

    return mb_strtoupper ( $first . $last );

  }

  // A form that posts back to the page, the way {input:form 'name'} writes one (lib/form.php): the
  // session's CSRF token when it posts here, the hidden padForm field with its name - so
  // padPosted ( 'name' ) tells it came back - and fields that refill from the post and show
  // the message padValidate found, while it stands on the stack of forms. $fields is a
  // function that writes the fields, called while it does.

  function padSectionForm ( $name, $class, $fields ) {

    global $padFormStack;

    [ $at, $place ] = padSectionFormPlace ( $name );

    $open = "<form method=\"post\" class=\"$class\">";

    if ( padCsrfFormPosts ( $open ) )
      $open .= padCsrfField ();

    $open .= '<input type="hidden" name="' . padFormName . '" value="' . padFormEscape ( $name ) . '">';

    if ( $at > 1 )
      $open .= '<input type="hidden" name="' . padFormAt . '" value="' . padFormEscape ( $place ) . '">';

    if ( isset ( $GLOBALS ['padCsrfIssued'] ) and str_contains ( $open, padCsrfField () ) )
      $GLOBALS ['padFormTokened'] [] = $open;

    $padFormStack [] = [ 'name' => $name, 'method' => 'post', 'open' => $open, 'client' => FALSE,
                         'at' => $at, 'place' => $place ];

    $html = $fields ();

    array_pop ( $padFormStack );

    return $open . $html . '</form>';

  }

  // A field of such a form: its label, the control - refilled when the form came back - and
  // the message padValidate left for it, tied to it for a screen reader. Its id is the
  // section's and the field's name - pad-section-newsletter-046be8dd-email - so two sections
  // of a kind on a page have fields of their own: both wrote newsletter-email, and the second
  // label named the first field.

  function padSectionInput ( $form, $name, $label, $type, $attrs = '' ) {

    $id    = padSectionId () . "-$name";
    $error = padFormError ( $name, $label );
    $value = padFormValue ( $name );
    $aria  = ( $error !== '' ) ? " aria-invalid=\"true\" aria-describedby=\"$id-error\"" : '';

    $control = ( $type == 'textarea' )
      ? "<textarea name=\"$name\" id=\"$id\" rows=\"5\"$attrs$aria>" . padFormEscape ( $value ) . '</textarea>'
      : "<input type=\"$type\" name=\"$name\" id=\"$id\" value=\"" . padFormEscape ( $value ) . "\"$attrs$aria>";

    return "<div class=\"ps-field\"><label for=\"$id\">" . padSectionEscape ( $label ) . "</label>$control"
         . ( $error !== '' ? "<span class=\"ps-error\" id=\"$id-error\">" . padFormEscape ( $error ) . '</span>' : '' )
         . '</div>';

  }

  // Whether a section's form came back right: padPosted holds - its name came back and the
  // rules on fields of the template found nothing - and padValidate, which the page's PHP ran
  // on the post, left no message for any of its fields - and it is this section's form that
  // was sent (padSectionFormPlace). Then the section says thanks in its place; else the form
  // shows again, refilled and with the messages when it was the one sent.

  function padSectionCameBack ( $form, $fields ) {

    global $padFormErrors, $padFormStack;

    if ( ! padPosted ( $form ) )
      return FALSE;

    [ $at, $place ] = padSectionFormPlace ( $form );

    $padFormStack [] = [ 'name' => $form, 'method' => 'post', 'open' => '', 'client' => FALSE, 'at' => $at, 'place' => $place ];
    $sent            = ( padFormSource () !== NULL );

    array_pop ( $padFormStack );

    if ( ! $sent )
      return FALSE;

    foreach ( $fields as $field )
      if ( isset ( $padFormErrors [$field] ) )
        return FALSE;

    return TRUE;

  }

  // Which form of its name on the page a section's form is - [ count, place ] - counted with
  // the {input:form}s of the name as padFormOpen counts them (lib/form.php), once per section: two
  // newsletter sections are one form, by its name, and the second carries its place, so only
  // the one that was sent says thanks, refills and shows the messages - both did after one
  // post.

  function padSectionFormPlace ( $name ) {

    global $padSectionNow, $padFormSeen;

    if ( ! isset ( $padSectionNow ['place'] ) ) {
      $at = $padFormSeen [$name] = ( $padFormSeen [$name] ?? 0 ) + 1;
      $padSectionNow ['place'] = [ $at, padFormPage () . ":$at" ];
    }

    return $padSectionNow ['place'];

  }

  // The name of a section's form - form= - letters, digits, _ and -.

  function padSectionFormName ( $default ) {

    global $padCheckSyntax;

    $name = padSectionOption ( 'form', $default );

    if ( preg_match ( '/^[A-Za-z][A-Za-z0-9_-]{0,63}$/D', $name ) )
      return $name;

    if ( $padCheckSyntax )
      padError ( "the " . padSectionWhat () . " has no form '" . padMakeSafe ( $name, 20 ) . "' - a name of letters, digits, _ and -" );

    return $default;

  }

  // The section around what its kind built: the style of the sections and of the kind once
  // per page, the <section> named by its heading, the tone and the alignment it was given,
  // and inside it the room the content takes. The kind's markup is protected - its braces
  // are text - and what it held comes back after.

  function padSection ( $kind, $inner, $class = '' ) {

    global $padSectionNow, $padCheckSyntax;

    if ( ! $padSectionNow ['listed'] and ( $padSectionNow ['items'] or $padSectionNow ['rows'] !== NULL ) and $padCheckSyntax )
      padError ( "the " . padSectionWhat () . " takes no items - data= and {section:item} belong to a section that lists them, as features, pricing or faq" );

    $id    = padSectionId ();
    $tone  = padSectionChoice ( 'tone', $padSectionNow ['tone'] ?? 'plain', [ 'plain', 'soft', 'accent' ] );
    $align = padSectionChoice ( 'align', $padSectionNow ['align'] ?? 'center', [ 'left', 'center' ] );
    $given = $padSectionNow ['given'] ?? '';

    $open = '<section class="pad-section pad-section-' . $kind . " ps-tone-$tone ps-align-$align" . ( $class !== '' ? " $class" : '' ) . '"'
          . ( $given !== '' ? " id=\"$given\"" : '' )
          . ( ( $padSectionNow ['titled'] ?? FALSE ) ? " aria-labelledby=\"$id-title\"" : '' ) . '>';

    $html = padProtect ( $open . '<div class="ps-inner">' . $inner . '</div></section>' );

    $held = $padSectionNow ['held'];

    for ( $i = 0; $i < 3 and str_contains ( $html, "\u{E0E2}" ); $i++ )
      $html = preg_replace_callback ( '/\x{E0E2}(\d+)\x{E0E3}/u', fn ( $m ) => $held [ (int) $m [1] ] ?? '', $html );

    return padSectionStyle ( $kind ) . $html;

  }

  // The style: the colours and the rules every section shares, then the kind's own, each
  // once per page with the CSP nonce. The colours fall back to the frame's palette - a page
  // in the frame, or under a theme, has them - and to light and dark steps of their own.

  function padSectionStyle ( $kind ) {

    $roles = [ 'bg'          => [ 'var(--bg,#ffffff)',          'var(--bg,#111318)'          ],
               'panel'       => [ 'var(--panel,#ffffff)',       'var(--panel,#1a1d24)'       ],
               'text'        => [ 'var(--text,#1d2330)',        'var(--text,#e5e7eb)'        ],
               'soft'        => [ 'var(--soft,#4b5563)',        'var(--soft,#c0c6d0)'        ],
               'dim'         => [ 'var(--dim,#6b7280)',         'var(--dim,#9ca3af)'         ],
               'line'        => [ 'var(--line,#e3e6eb)',        'var(--line,#2a303b)'        ],
               'accent'      => [ 'var(--accent,#2563eb)',      'var(--accent,#60a5fa)'      ],
               'accent-text' => [ 'var(--accent-text,#ffffff)', 'var(--accent-text,#0b1020)' ],
               'accent-soft' => [ 'var(--accent-soft,#e8effd)', 'var(--accent-soft,#172235)' ],
               'bad'         => [ 'var(--bad,#b91c1c)',         'var(--bad,#f87171)'         ] ];

    $css = padWidgetStyle ( 'section', $roles, padSectionAsset ( 'section.css' ) );

    if ( padWidgetOnce ( "style:section-$kind" ) )
      $css .= padWidgetSheet ( padSectionAsset ( "$kind.css" ) );

    return $css;

  }

  // A style sheet of www/assets/sections/ (padWidgetFile): linked, or - written in - a rule a
  // line sent as one line, the line breaks and the indent after them taken out as
  // padWidgetAsset sends the widgets', its comments left out.

  function padSectionAsset ( $file ) {

    return padWidgetFile ( "sections/$file", 'bare' );

  }

?>
