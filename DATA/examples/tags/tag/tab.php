<?php

  $tagAbout   = 'One item of a tabs, accordion or carousel: its label and its content, handed to that owner.';
  $tagGroup   = 'widgets';
  $tagForm    = 'pair';

  $tagSyntax  = <<<'PAD'
{tab 'label'} ... {/tab}
PAD;

  $tagParms   = [
    'label' => 'The tab\'s label, the summary of an accordion item or the caption of a carousel slide. Required in <code>{tabs}</code> and <code>{accordion}</code>, optional in <code>{carousel}</code>.' ];

  $tagOptions = [];

  $tagSee     = [ 'tabs', 'accordion', 'carousel' ];

?>
