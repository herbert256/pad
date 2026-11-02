<?php

  // {section:comparison title='Compare the plans', plans='Hobby, Pro, Company'} {section:item
  // 'Applications', values='3; Unlimited; Unlimited'/} ... {/section:comparison} - the plans or
  // products side by side in a table, a row per feature and a column per plan, each cell what
  // the plan has of it: a value as it is written, a check for yes, a dash for no and a question
  // mark for a value nobody gave - each read out as its word (included=, excluded=, unknown=;
  // Included, Not included, Unknown), so a missing value is never a blank cell. The plans are
  // plans= (a list, a comma between them) or else the fields of the first row of data= after
  // feature, category and text. The features are the rows of data= - their fields feature,
  // category, text and a field per plan - or {section:item} pairs or single tags, the item's
  // first value the feature, values= its values in the order of the plans (a ; or a line
  // between them, or a list) or the lines of its content, a line a plan; an option of a plan's
  // name gives that plan's value. category= gathers the features under a heading row, in the
  // order the categories first come; text= is a line under the feature's name. featured=
  // names the plan drawn in the accent, badge= a word over its name. The table scrolls
  // sideways in a section too narrow for it, the features' column standing still, so a value is
  // never parted from its feature and its plan.
  //
  // Parts: eyebrow, title, text, note. Options: title (or the first value), eyebrow, text,
  // note, plans, featured, badge, included, excluded, unknown, data, level, tone, align, id.

  padSectionParts ( [ 'eyebrow', 'title', 'text', 'note' ] );

  include_once PAD . 'subs/icons/_lib/icon.php';

  $padSecHead  = padSectionHead ();
  $padSecNote  = padSectionPart ( 'note' );
  $padSecId    = padSectionId ();
  $padSecRows  = $GLOBALS ['padSectionNow'] ['rows'];
  $padSecWords = [ 'yes'     => padSectionOption ( 'included', 'Included' ),
                   'no'      => padSectionOption ( 'excluded', 'Not included' ),
                   'unknown' => padSectionOption ( 'unknown',  'Unknown' ) ];

  // The plans: plans=, a list or a text with a comma between them, else the fields of the
  // first row that are not the feature's own.

  $padSecPlans = padTagParm ( 'plans', '' );
  $padSecPlans = is_array ( $padSecPlans ) ? $padSecPlans : ( is_scalar ( $padSecPlans ) ? explode ( ',', padParmText ( (string) $padSecPlans ) ) : [] );
  $padSecPlans = array_values ( array_filter ( array_map ( fn ( $one ) => is_scalar ( $one ) ? trim ( (string) $one ) : '', $padSecPlans ), 'strlen' ) );

  if ( ! $padSecPlans and is_array ( $padSecRows ) and $padSecRows ) {
    $padSecFirst = reset ( $padSecRows );
    $padSecFirst = is_object ( $padSecFirst ) ? padToArray ( $padSecFirst ) : $padSecFirst;
    if ( is_array ( $padSecFirst ) )
      foreach ( array_keys ( $padSecFirst ) as $padSecKey )
        if ( ! in_array ( (string) $padSecKey, [ 'feature', 'category', 'text', 'values' ], TRUE ) )
          $padSecPlans [] = (string) $padSecKey;
  }

  if ( count ( $padSecPlans ) != count ( array_unique ( $padSecPlans ) ) and $GLOBALS ['padCheckSyntax'] )
    padError ( "the " . padSectionWhat () . " names a plan twice - " . implode ( ', ', array_map ( fn ( $one ) => padMakeSafe ( $one, 20 ), $padSecPlans ) ) );

  $padSecPlans = array_values ( array_unique ( $padSecPlans ) );

  if ( ! $padSecPlans and $GLOBALS ['padCheckSyntax'] )
    padError ( "the " . padSectionWhat () . " has no plans to compare - plans='Hobby, Pro' or the fields of the rows of data=" );

  // A plan of plans= that no row of data= has a field for is a name written wrong.

  if ( is_array ( $padSecRows ) and $padSecRows and $GLOBALS ['padCheckSyntax'] )
    foreach ( $padSecPlans as $padSecPlan ) {
      $padSecFound = FALSE;
      foreach ( $padSecRows as $padSecRow ) {
        $padSecRow = is_object ( $padSecRow ) ? padToArray ( $padSecRow ) : $padSecRow;
        if ( is_array ( $padSecRow ) and array_key_exists ( $padSecPlan, $padSecRow ) )
          $padSecFound = TRUE;
      }
      if ( ! $padSecFound )
        padError ( "the " . padSectionWhat () . " has no field '" . padMakeSafe ( $padSecPlan, 20 ) . "' in the rows of its data= for the plan of that name" );
    }

  $padSecFeatured = padSectionOption ( 'featured' );

  if ( $padSecFeatured !== '' and ! in_array ( $padSecFeatured, $padSecPlans, TRUE ) ) {
    if ( $GLOBALS ['padCheckSyntax'] )
      padError ( "the " . padSectionWhat () . " has no plan '" . padMakeSafe ( $padSecFeatured, 20 ) . "' to feature - " . implode ( ', ', array_map ( fn ( $one ) => padMakeSafe ( $one, 20 ), $padSecPlans ) ) );
    $padSecFeatured = '';
  }

  $padSecBadge = padSectionOption ( 'badge' );
  $padSecItems = padSectionItems ( 'feature', array_values ( array_unique ( array_merge ( [ 'feature', 'category', 'text', 'values' ], $padSecPlans ) ) ), 'values', 'features' );

  // The items begin with the rows, one each and in their order: a row's true and false - JSON's
  // - are read from the row itself, as the items hold its values as text.

  $padSecOrigin = is_array ( $padSecRows ) ? array_values ( $padSecRows ) : [];

  // A value as its kind - yes, no, unknown - or as itself: a check, a dash or a question mark,
  // each with its word for a screen reader and for a copy of the table, or the value.

  $padSecCell = function ( $value, $html ) use ( $padSecWords ) {

    if ( $value === TRUE )
      $kind = 'yes';
    elseif ( $value === FALSE )
      $kind = 'no';
    else {
      $text = mb_strtolower ( trim ( preg_replace ( '/\s+/u', ' ', (string) $value ) ) );
      $kind = match ( TRUE ) {
        in_array ( $text, [ 'yes', 'true', '✓', '✔', 'included' ], TRUE )           => 'yes',
        in_array ( $text, [ 'no', 'false', '-', '–', '—', '✗', '✕', 'excluded' ], TRUE ) => 'no',
        in_array ( $text, [ '', '?', 'unknown' ], TRUE )                              => 'unknown',
        default                                                                       => ''
      };
    }

    return match ( $kind ) {
      'yes'     => '<span class="ps-cmp-yes">' . padIcon ( 'check', 20 ) . '<span class="ps-sr">' . padSectionEscape ( $padSecWords ['yes'] ) . '</span></span>',
      'no'      => '<span class="ps-cmp-no">' . padIcon ( 'minus', 20 ) . '<span class="ps-sr">' . padSectionEscape ( $padSecWords ['no'] ) . '</span></span>',
      'unknown' => '<span class="ps-cmp-unknown"><span aria-hidden="true">?</span><span class="ps-sr">' . padSectionEscape ( $padSecWords ['unknown'] ) . '</span></span>',
      default   => $html
    };

  };

  // The rows, gathered by their category where it first comes; a feature without one stands
  // before the first category.

  $padSecGroups = [ '' => [] ];

  foreach ( $padSecItems as $padSecI => $padSecItem ) {

    $padSecRow = $padSecOrigin [$padSecI] ?? NULL;
    $padSecRow = is_object ( $padSecRow ) ? padToArray ( $padSecRow ) : $padSecRow;

    // The values in the order of the plans: values= or the lines of the content, then an option
    // or a field of a plan's name for that plan.

    $padSecValues = [];

    if ( isset ( $padSecItem ['html'] ['values'] ) ) {
      foreach ( preg_split ( '/\R/', $padSecItem ['html'] ['values'] ) as $padSecLine )
        if ( trim ( $padSecLine ) !== '' )
          $padSecValues [] = [ html_entity_decode ( strip_tags ( padUnprotect ( trim ( $padSecLine ) ) ), ENT_QUOTES | ENT_HTML5, 'UTF-8' ), padSectionHold ( trim ( $padSecLine ) ) ];
    } elseif ( isset ( $padSecItem ['raw'] ['values'] ) ) {
      $padSecGiven = $padSecItem ['raw'] ['values'];
      foreach ( is_array ( $padSecGiven ) ? $padSecGiven : preg_split ( '/\R|;/', (string) $padSecGiven ) as $padSecOne )
        $padSecValues [] = [ is_bool ( $padSecOne ) ? $padSecOne : ( is_scalar ( $padSecOne ) ? trim ( (string) $padSecOne ) : '' ),
                             is_scalar ( $padSecOne ) ? padSectionEscape ( trim ( (string) $padSecOne ) ) : '' ];
    }

    if ( count ( $padSecValues ) > count ( $padSecPlans ) ) {
      if ( $GLOBALS ['padCheckSyntax'] )
        padError ( "a {section:item} of the " . padSectionWhat () . " has " . count ( $padSecValues ) . " values for " . count ( $padSecPlans )
                 . " plans - a value a plan, in the order of plans=" );
      $padSecValues = array_slice ( $padSecValues, 0, count ( $padSecPlans ) );
    }

    $padSecCells = '';

    foreach ( $padSecPlans as $padSecN => $padSecPlan ) {

      [ $padSecValue, $padSecHtml ] = $padSecValues [$padSecN] ?? [ '', '' ];

      if ( array_key_exists ( $padSecPlan, $padSecItem ['raw'] ) and ! in_array ( $padSecPlan, [ 'feature', 'category', 'text', 'values' ], TRUE ) ) {
        $padSecOne   = ( is_array ( $padSecRow ) and is_bool ( $padSecRow [$padSecPlan] ?? NULL ) ) ? $padSecRow [$padSecPlan] : $padSecItem ['raw'] [$padSecPlan];
        $padSecValue = is_bool ( $padSecOne ) ? $padSecOne : ( is_scalar ( $padSecOne ) ? trim ( (string) $padSecOne ) : '' );
        $padSecHtml  = is_scalar ( $padSecOne ) ? padSectionEscape ( trim ( (string) $padSecOne ) ) : '';
      }

      $padSecCells .= '<td' . ( $padSecPlan === $padSecFeatured ? ' class="ps-cmp-featured"' : '' ) . '>' . $padSecCell ( $padSecValue, $padSecHtml ) . '</td>';

    }

    $padSecText     = padSectionField ( $padSecItem, 'text' );
    $padSecCategory = trim ( (string) ( is_scalar ( padSectionRaw ( $padSecItem, 'category' ) ) ? padSectionRaw ( $padSecItem, 'category' ) : '' ) );

    $padSecGroups [$padSecCategory] [] = '<tr><th scope="row">' . padSectionField ( $padSecItem, 'feature' )
                                       . ( $padSecText !== '' ? "<span class=\"ps-cmp-text\">$padSecText</span>" : '' ) . "</th>$padSecCells</tr>";

  }

  // The head: an empty corner over the features, then a column header a plan.

  $padSecHeads = '';

  foreach ( $padSecPlans as $padSecPlan )
    $padSecHeads .= '<th scope="col"' . ( $padSecPlan === $padSecFeatured ? ' class="ps-cmp-featured"' : '' ) . '>'
                  . ( ( $padSecPlan === $padSecFeatured and $padSecBadge !== '' ) ? '<span class="ps-cmp-badge">' . padSectionEscape ( $padSecBadge ) . '</span>' : '' )
                  . padSectionEscape ( $padSecPlan ) . '</th>';

  $padSecBody = '';
  $padSecSpan = count ( $padSecPlans ) + 1;

  foreach ( $padSecGroups as $padSecCategory => $padSecLines ) {

    if ( ! $padSecLines )
      continue;

    $padSecBody .= '<tbody>'
                 . ( $padSecCategory !== '' ? "<tr class=\"ps-cmp-category\"><th scope=\"rowgroup\" colspan=\"$padSecSpan\"><span>" . padSectionEscape ( $padSecCategory ) . '</span></th></tr>' : '' )
                 . implode ( '', $padSecLines ) . '</tbody>';

  }

  // The table in a box that scrolls sideways, which takes the focus so a keyboard scrolls it
  // too, named by the section's heading.

  $padSecName = ( $GLOBALS ['padSectionNow'] ['titled'] ?? FALSE ) ? " aria-labelledby=\"$padSecId-title\"" : ' aria-label="Comparison"';

  return padSection ( 'comparison', $padSecHead
                                  . "<div class=\"ps-cmp-scroll\" role=\"group\" tabindex=\"0\"$padSecName>"
                                  . '<table class="ps-cmp"><thead><tr><td class="ps-cmp-corner"></td>' . $padSecHeads . '</tr></thead>' . $padSecBody . '</table></div>'
                                  . ( $padSecNote !== '' ? "<p class=\"ps-note ps-cmp-note\">$padSecNote</p>" : '' ) );

?>
