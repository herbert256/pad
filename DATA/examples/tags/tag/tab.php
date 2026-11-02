<?php

  $tagAbout   = 'One item of a tabs, accordion, carousel, wizard, lightbox, splitpane or sortable widget: its label and its content, handed to that owner.';
  $tagGroup   = 'widgets';
  $tagForm    = 'pair';

  $tagSyntax  = <<<'PAD'
{pad:tab 'label'} ... {/pad:tab}
PAD;

  $tagParms   = [
    'label' => 'The tab\'s label, the summary of an accordion item or the caption of a carousel slide. Required in <code>{widget:tabs}</code>, <code>{widget:accordion}</code> and <code>{widget:splitpane}</code>, optional in <code>{widget:carousel}</code>; in <code>{widget:sortable}</code> the key the item is posted under.' ];

  $tagOptions = [];

  $tagSee     = [ 'widget:tabs' ];

?>
