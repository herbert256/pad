<?php

  $tagAbout   = 'Renders its content only when a condition holds, with elseif and else branches.';
  $tagGroup   = 'conditions';
  $tagForm    = 'pair';

  $tagSyntax  = <<<'PAD'
{pad:if condition} ... {/pad:if}
{pad:if condition} ... {pad:else} ... {/pad:if}
{pad:if condition} ... {pad:elseif condition} ... {pad:else} ... {/pad:if}
PAD;

  $tagParms   = [
    'condition' => 'An expression. A comparison (<code>$count gt 0</code>) or a bare value, tested for its truth as PHP does: <code>\'\'</code>, <code>\'0\'</code>, <code>0</code> and FALSE fail.' ];

  $tagOptions = [];

  $tagSee     = [ 'case', 'ifchanged', 'true', 'false' ];

?>
