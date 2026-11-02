<?php

  $tagAbout   = 'Renders the one branch whose {pad:when} matches a value, with an {pad:else} for none.';
  $tagGroup   = 'conditions';
  $tagForm    = 'pair';

  $tagSyntax  = <<<'PAD'
{pad:case value} {pad:when 'a'} ... {pad:when 'b', 'c'} ... {/pad:case}
{pad:case value} {pad:when 'a'} ... {pad:else} ... {/pad:case}
PAD;

  $tagParms   = [
    'value' => 'An expression - a field such as <code>$color</code>, or any computed value - that each <code>{pad:when}</code> is compared with.' ];

  $tagOptions = [];

  $tagSee     = [ 'if', 'ifchanged', 'switch' ];

?>
