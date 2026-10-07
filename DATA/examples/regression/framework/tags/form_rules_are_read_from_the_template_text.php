<?php

  $text = <<<'PAD'
{form 'one'}
  {input required, 'email', type='email', rules='required|email'}
  {~input 'name', rules = "required|max:100"~}
  {# {input 'gone', rules='required'} #}
  {-- {input 'gone', rules='required'} --}
  {ignore}{input 'kept', rules='bogus'}{/ignore}
  {textarea 'body', rows=4, rules='required|regex:/^\\w&open;2&close;$/'}
  {input 'plain', label='rules'}
{/form}
{form "two"}{input 'q', rules='min:2' | trim}{/form}
{form 'one'}{input 'email', rules='required|email'}{/form}
PAD;

  $rulesRead = json_encode ( padFormRulesOf ( $text ), JSON_UNESCAPED_SLASHES );

?>
