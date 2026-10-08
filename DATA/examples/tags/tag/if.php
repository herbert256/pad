<?php

  $tagAbout   = 'Renders its content only when a condition holds, with elseif and else branches.';
  $tagGroup   = 'conditions';
  $tagForm    = 'pair';

  $tagSyntax  = <<<'PAD'
{if condition} ... {/if}
{if condition} ... {else} ... {/if}
{if condition} ... {elseif condition} ... {else} ... {/if}
PAD;

  $tagParms   = [
    'condition' => 'An expression. A comparison (<code>$count gt 0</code>) or a bare value, tested for its truth as PHP does: <code>\'\'</code>, <code>\'0\'</code>, <code>0</code> and FALSE fail.' ];

  $tagOptions = [];

  $tagSee     = [ 'case', 'ifchanged', 'true', 'false' ];

?>
