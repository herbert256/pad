<?php

  // {section:pricing title='heading'} {section:item 'Pro', price='€12', period='month',
  // featured}...{/section:item} ... {/section:pricing} - the plans side by side, a card each:
  // its name, its price and period, a line about it, what it includes, and a button. One plan
  // can be featured - marked with a badge (badge=, 'Most popular') and drawn in the accent.
  // The plans are the rows of data= - their fields name, price, period, text, features (a
  // list, or a text with a line or a ; between them), button, link, featured and badge - or
  // {section:item} pairs, the item's first value its name and the lines of its content what it
  // includes, a line a feature (or <li>s of its own). A plan's button shows when it has a link
  // (button=, 'Choose').
  //
  // Parts: eyebrow, title, text, note. Options: title (or the first value), eyebrow, text,
  // note, columns, data, level, tone, align, id.

  padSectionParts ( [ 'eyebrow', 'title', 'text', 'note' ] );

  $padSecHead  = padSectionHead ();
  $padSecNote  = padSectionPart ( 'note' );
  $padSecItems = padSectionItems ( 'name', [ 'name', 'price', 'period', 'text', 'features', 'button', 'link', 'featured', 'badge' ], 'features', 'plans' );
  $padSecCols  = padSectionInt ( 'columns', max ( 1, min ( 4, count ( $padSecItems ) ) ), 1, 6 );
  $padSecLevel = min ( 6, padSectionLevel () + 1 );
  $padSecId    = padSectionId ();
  $padSecCards = '';

  foreach ( $padSecItems as $padSecN => $padSecItem ) {

    $padSecFeatured = padSectionTrue ( padSectionRaw ( $padSecItem, 'featured', FALSE ) );
    $padSecName     = padSectionField ( $padSecItem, 'name' );
    $padSecPrice    = padSectionField ( $padSecItem, 'price' );
    $padSecPeriod   = trim ( (string) padSectionRaw ( $padSecItem, 'period' ) );
    $padSecText     = padSectionField ( $padSecItem, 'text' );
    $padSecList     = padSectionList ( $padSecItem, 'features' );
    $padSecLink     = padSectionLink ( padSectionRaw ( $padSecItem, 'link' ) );
    $padSecButton   = padSectionHas ( $padSecItem, 'button' ) ? padSectionField ( $padSecItem, 'button' ) : 'Choose';
    $padSecBadge    = padSectionHas ( $padSecItem, 'badge' ) ? padSectionField ( $padSecItem, 'badge' ) : 'Most popular';
    $padSecPlanId   = "$padSecId-plan-" . ( $padSecN + 1 );

    // A period of one word - month, year - reads "/ month"; one written out - per seat a
    // month - stands as it is.

    if ( $padSecPeriod !== '' )
      $padSecPeriod = preg_match ( '/^[\p{L}]+$/u', $padSecPeriod ) ? '/&#160;' . padSectionEscape ( $padSecPeriod ) : padSectionEscape ( $padSecPeriod );

    $padSecCards .= '<li class="ps-card ps-plan' . ( $padSecFeatured ? ' ps-plan-featured' : '' ) . '">'
                  . ( $padSecFeatured ? "<p class=\"ps-plan-badge\">$padSecBadge</p>" : '' )
                  . "<h$padSecLevel class=\"ps-plan-name\" id=\"$padSecPlanId\">$padSecName</h$padSecLevel>"
                  . ( $padSecPrice !== '' ? "<p class=\"ps-plan-price\"><span class=\"ps-plan-amount\">$padSecPrice</span>"
                                            . ( $padSecPeriod !== '' ? " <span class=\"ps-plan-period\">$padSecPeriod</span>" : '' ) . '</p>' : '' )
                  . ( $padSecText !== '' ? padSectionTextBlock ( $padSecText, 'ps-plan-text' ) : '' )
                  . ( $padSecList ? '<ul class="ps-plan-features">' . $padSecList [0] . '</ul>' : '' )
                  . ( $padSecLink !== '' ? "<a class=\"ps-button" . ( $padSecFeatured ? '' : ' ps-button-second' ) . "\" href=\"$padSecLink\" aria-describedby=\"$padSecPlanId\">$padSecButton</a>" : '' )
                  . '</li>';

  }

  return padSection ( 'pricing', $padSecHead . "<ul class=\"ps-grid ps-cols-$padSecCols ps-plans\">$padSecCards</ul>"
                               . ( $padSecNote !== '' ? "<p class=\"ps-note ps-plans-note\">$padSecNote</p>" : '' ) );

?>
