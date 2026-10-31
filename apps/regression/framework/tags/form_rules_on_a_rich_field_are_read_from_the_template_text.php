<?php

  // The rules of the rich fields are read with {input}'s before the page's PHP runs: the
  // input: type and the bare name, a field written as a pair - its content is no rules - and
  // a closing tag that is no field.

  $text = "{form 'f'}{input:slider 'volume', rules='integer|between:1,9'/}"
        . "{stars 'score', rules='required'/}"
        . "{combobox 'city', rules='in:Rome,Oslo'}Rome\nOslo{/combobox}"
        . "{otp 'code', rules='required|digits:6'/}{/form}";

  $rulesRead = json_encode ( padFormRulesOf ( $text ), JSON_UNESCAPED_SLASHES );

?>
