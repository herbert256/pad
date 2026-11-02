<?php

  // {section:changelog title='What changed'} {section:item '2.1.0', date='2026-10-30',
  // added='Comparison tables; An agenda', fixed='The footer on a phone'}The sections
  // release.{/section:item} ... {/section:changelog} - the releases, in the order they are
  // written - newest first, as a changelog keeps them; never sorted by their version, whose
  // order a sort cannot know - each with its version, its date and its changes in the groups
  // of Keep a Changelog: Added, Changed, Deprecated, Removed, Fixed and Security, each a list.
  // The releases are the rows of data= - their fields version, date, title, text, the six
  // groups (a list, or a text with a line or a ; between them), link and badge - or
  // {section:item} pairs or single tags, the item's first value the version and its content
  // the text. A release's date is written 2026-10-30, as Keep a Changelog writes it; its
  // title is a name beside the version, link= where the version leads (a release page, a
  // comparison), badge= a word beside it - Latest, Yanked. Every release is an anchor named by
  // its version alone, so a link to it stays when releases are added: #v2.1.0, #Unreleased -
  // the section's id= before it when given; a version given twice is reported.
  //
  // Parts: eyebrow, title, text. Options: title (or the first value), eyebrow, text, data,
  // level, tone, align (left), id.

  padSectionParts ( [ 'eyebrow', 'title', 'text' ] );

  $GLOBALS ['padSectionNow'] ['align'] = 'left';

  $padSecGroups = [ 'added' => 'Added', 'changed' => 'Changed', 'deprecated' => 'Deprecated',
                    'removed' => 'Removed', 'fixed' => 'Fixed', 'security' => 'Security' ];

  $padSecHead  = padSectionHead ();
  $padSecItems = padSectionItems ( 'version', array_merge ( [ 'version', 'date', 'title', 'text' ], array_keys ( $padSecGroups ), [ 'link', 'badge' ] ), 'text', 'releases' );
  $padSecLevel = min ( 6, padSectionLevel () + 1 );
  $padSecSub   = min ( 6, $padSecLevel + 1 );
  $padSecGiven = padSectionOption ( 'id' );
  $padSecSeen  = [];
  $padSecList  = '';

  foreach ( $padSecItems as $padSecItem ) {

    $padSecVersion = trim ( (string) ( is_scalar ( padSectionRaw ( $padSecItem, 'version' ) ) ? padSectionRaw ( $padSecItem, 'version' ) : '' ) );

    if ( $padSecVersion === '' ) {
      if ( $GLOBALS ['padCheckSyntax'] )
        padError ( "a release of the " . padSectionWhat () . " has no version - the item's first value, or the field version of its row" );
      continue;
    }

    // The anchor: the version, a run of other characters than letters, digits, . _ and - made
    // one -, and a v before a version that starts with a digit - v2.1.0.

    $padSecAnchor = trim ( preg_replace ( '/[^A-Za-z0-9._-]+/', '-', $padSecVersion ), '-.' );
    $padSecAnchor = ( $padSecAnchor === '' or ! ctype_alpha ( $padSecAnchor [0] ) ) ? "v$padSecAnchor" : $padSecAnchor;
    $padSecAnchor = ( $padSecGiven !== '' ) ? "$padSecGiven-$padSecAnchor" : $padSecAnchor;

    if ( isset ( $padSecSeen [$padSecAnchor] ) ) {
      if ( $GLOBALS ['padCheckSyntax'] )
        padError ( "the " . padSectionWhat () . " has the release '" . padMakeSafe ( $padSecVersion, 20 ) . "' twice - its version is its anchor, #$padSecAnchor" );
      continue;
    }

    $padSecSeen [$padSecAnchor] = TRUE;

    // The date as Keep a Changelog writes it, 2026-10-30, and as a <time> a machine reads.

    $padSecDate = trim ( (string) ( is_scalar ( padSectionRaw ( $padSecItem, 'date' ) ) ? padSectionRaw ( $padSecItem, 'date' ) : '' ) );

    if ( $padSecDate !== '' and ! ( preg_match ( '/^(\d{4})-(\d{2})-(\d{2})$/D', $padSecDate, $padSecDay ) and checkdate ( (int) $padSecDay [2], (int) $padSecDay [3], (int) $padSecDay [1] ) ) ) {
      if ( $GLOBALS ['padCheckSyntax'] )
        padError ( "the release '" . padMakeSafe ( $padSecVersion, 20 ) . "' of the " . padSectionWhat () . " has no date '" . padMakeSafe ( $padSecDate, 20 ) . "' - a day written 2026-10-30" );
      $padSecDate = '';
    }

    $padSecLink    = padSectionLink ( padSectionRaw ( $padSecItem, 'link' ) );
    $padSecName    = padSectionField ( $padSecItem, 'title' );
    $padSecText    = padSectionField ( $padSecItem, 'text' );
    $padSecBadge   = padSectionField ( $padSecItem, 'badge' );
    $padSecHeading = padSectionEscape ( $padSecVersion );
    $padSecHeading = "<a href=\"" . ( $padSecLink !== '' ? $padSecLink : "#$padSecAnchor" ) . "\">$padSecHeading</a>";

    $padSecChanges = '';

    foreach ( $padSecGroups as $padSecGroup => $padSecLabel )
      if ( $padSecLines = padSectionList ( $padSecItem, $padSecGroup ) )
        $padSecChanges .= "<h$padSecSub class=\"ps-release-group ps-release-$padSecGroup\">$padSecLabel</h$padSecSub><ul>" . $padSecLines [0] . '</ul>';

    $padSecList .= "<li class=\"ps-release\" id=\"$padSecAnchor\">"
                 . '<div class="ps-release-meta">'
                 . "<h$padSecLevel class=\"ps-release-version\">$padSecHeading</h$padSecLevel>"
                 . ( $padSecDate  !== '' ? "<time datetime=\"$padSecDate\">$padSecDate</time>" : '' )
                 . ( $padSecBadge !== '' ? "<span class=\"ps-release-badge\">$padSecBadge</span>" : '' )
                 . '</div><div class="ps-release-body">'
                 . ( $padSecName  !== '' ? "<p class=\"ps-release-name\">$padSecName</p>" : '' )
                 . ( $padSecText  !== '' ? padSectionTextBlock ( $padSecText, 'ps-item-text' ) : '' )
                 . $padSecChanges
                 . '</div></li>';

  }

  return padSection ( 'changelog', $padSecHead . "<ol class=\"ps-releases\">$padSecList</ol>" );

?>
