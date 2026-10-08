<?php

  $tagAbout   = 'Renders the one branch whose {when} matches a value, with an {else} for none.';
  $tagGroup   = 'conditions';
  $tagForm    = 'pair';

  $tagSyntax  = <<<'PAD'
{case value} {when 'a'} ... {when 'b', 'c'} ... {/case}
{case value} {when 'a'} ... {else} ... {/case}
PAD;

  $tagParms   = [
    'value' => 'An expression - a field such as <code>$color</code>, or any computed value - that each <code>{when}</code> is compared with.' ];

  $tagOptions = [];

  $tagSee     = [ 'if', 'ifchanged', 'switch' ];

?>
