<?php

  $text = <<<'PAD'
{input:form 'one'}
  {input:field required, 'email', type='email', rules='required|email'}
  {~input:field 'name', rules = "required|max:100"~}
  {# {input:field 'gone', rules='required'} #}
  {-- {input:field 'gone', rules='required'} --}
  {pad:ignore}{input:field 'kept', rules='bogus'}{/pad:ignore}
  {input:textarea 'body', rows=4, rules='required|regex:/^\\w&open;2&close;$/'}
  {input:field 'plain', label='rules'}
{/input:form}
{input:form "two"}{input:field 'q', rules='min:2' | trim}{/input:form}
{input:form 'one'}{input:field 'email', rules='required|email'}{/input:form}
PAD;

  $rulesRead = json_encode ( padFormRulesOf ( $text ), JSON_UNESCAPED_SLASHES );

?>
