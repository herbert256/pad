<?php

  $tagAbout   = 'Renders its content while a feature flag is on for this visitor, and its else half while it is off.';
  $tagGroup   = 'access';
  $tagForm    = 'pair';

  $tagSyntax  = <<<'PAD'
{feature 'flag'} ... {/feature}
{feature 'flag'} ... {else} ... {/feature}
PAD;

  $tagParms   = [
    'flag' => 'A name in <code>$padFeatures</code>: <code>TRUE</code>, <code>FALSE</code>, a share from 0 to 1 of the visitors, or the name of a function given the user.' ];

  $tagOptions = [];

  $tagSee     = [ 'can', 'auth', 'if' ];

?>
