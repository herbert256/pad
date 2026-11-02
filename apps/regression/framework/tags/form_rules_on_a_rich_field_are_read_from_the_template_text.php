<?php

  // The rules of the rich fields are read with {pad:input}'s before the page's PHP runs: the
  // input: type and the bare name, a field written as a pair - its content is no rules - and
  // a closing tag that is no field.

  $text = "{pad:form 'f'}{input:slider 'volume', rules='integer|between:1,9'/}"
        . "{input:stars 'score', rules='required'/}"
        . "{input:combobox 'city', rules='in:Rome,Oslo'}Rome\nOslo{/input:combobox}"
        . "{input:otp 'code', rules='required|digits:6'/}{/pad:form}";

  $rulesRead = json_encode ( padFormRulesOf ( $text ), JSON_UNESCAPED_SLASHES );

?>
