<?php

  // The content of a rich field written as a pair is taken as written and never rendered, so
  // the fields written in it are text: the markdown editor's example field, an {input} among
  // the choices of a combobox and a slider inside a tag input carry no rules of the form.

  $text = "{form 'f'}{mdeditor 'body', label='Body', rules='required'}Write a field like {input 'email', rules='required|email'/} in a template.{/mdeditor}"
        . "{combobox 'city', rules='in:Rome,Oslo'}Rome\n{input 'x', rules='required'/}\nOslo{/combobox}"
        . "{input:taginput 'tags'}{slider 'v', rules='integer'/}{/input:taginput}"
        . "{input 'name', rules='required'/}<button>Save</button>{/form}";

  $rulesRead = json_encode ( padFormRulesOf ( $text ), JSON_UNESCAPED_SLASHES );

?>
