<?php

  // {widget:wizard}{pad:tab 'Address'}...{/pad:tab}{pad:tab 'Payment'}...{/pad:tab}{/widget:wizard} - a form or a story in
  // steps, one shown at a time: the steps in a row above it, numbered, the ones done and the
  // current one marked, and under each step a Back and a Next. Radio buttons and CSS, as
  // {widget:tabs} is - no script needed. active= names the step shown first, by its number from 1 or
  // its label; next= and back= are the texts of the two buttons, label= names the wizard for
  // a screen reader ('Steps'). subs/widgets/_lib/wizard.php; the {pad:tab}s are collected as {widget:tabs}
  // collects them, subs/widgets/_lib/tabs.php.
  //
  // Like {pad:live} it runs twice: first it opens the collection its {pad:tab}s hand their content
  // to and asks for the end walk, then it builds the steps from what they handed over.

  if ( $padWalk [$pad] == 'start' ) {

    if ( ! $padPair [$pad] and $padCheckSyntax )
      padError ( "the pair {widget:wizard} never closes" );

    padTabsOpen ( 'wizard' );

    $padWalk [$pad] = 'end';

    return TRUE;

  }

  $padWizItems = padTabsItems ( 'wizard', $padContent );
  $padWizOn    = padTabsPick ( $padWizItems, padTagParm ( 'active' ), 'wizard', 'active' );
  $padWizLabel = padWidgetText ( 'label', 'Steps' );

  $padContent = $padWizItems
    ? padWizard ( $padWizItems, $padWizOn, $padWizLabel !== '' ? $padWizLabel : 'Steps',
                  padWidgetText ( 'next', 'Next' ) ?: 'Next', padWidgetText ( 'back', 'Back' ) ?: 'Back' )
    : '';

  return TRUE;

?>
