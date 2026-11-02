<?php

  $text = <<<'PAD'
{pad:form 'one'}
  {pad:input required, 'email', type='email', rules='required|email'}
  {~pad:input 'name', rules = "required|max:100"~}
  {# {pad:input 'gone', rules='required'} #}
  {-- {pad:input 'gone', rules='required'} --}
  {pad:ignore}{pad:input 'kept', rules='bogus'}{/pad:ignore}
  {pad:textarea 'body', rows=4, rules='required|regex:/^\\w&open;2&close;$/'}
  {pad:input 'plain', label='rules'}
{/pad:form}
{pad:form "two"}{pad:input 'q', rules='min:2' | trim}{/pad:form}
{pad:form 'one'}{pad:input 'email', rules='required|email'}{/pad:form}
PAD;

  $rulesRead = json_encode ( padFormRulesOf ( $text ), JSON_UNESCAPED_SLASHES );

?>
