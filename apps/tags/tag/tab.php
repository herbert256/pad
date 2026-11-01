<?php

  $tagAbout   = 'One item of a tabs, accordion, carousel, wizard, lightbox or splitpane widget: its label and its content, handed to that owner.';
  $tagGroup   = 'widgets';
  $tagForm    = 'pair';

  $tagSyntax  = <<<'PAD'
{tab 'label'} ... {/tab}
PAD;

  $tagParms   = [
    'label' => 'The tab\'s label, the summary of an accordion item or the caption of a carousel slide. Required in <code>{tabs}</code>, <code>{accordion}</code> and <code>{splitpane}</code>, optional in <code>{carousel}</code>.' ];

  $tagOptions = [];

  $tagSee     = [ 'widget' ];

?>
