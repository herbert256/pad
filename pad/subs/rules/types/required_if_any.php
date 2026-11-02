<?php

  // required_if_any - `required_if_any:delivery,ship;plan,pro` requires a filled value when any semicolon-separated condition matches. Each condition is the existing field,value[,value...] contract; up to 20. Field references support nested bracket names. Missing/empty siblings never match empty values; values are trimmed and case sensitive. Lists match if any scalar item matches. Empty submitted lists use existing required conventions. Dependencies list every referenced field; message uses the normal localisation path.
  //
  // ## Example
  //
  // ```pad
  // {input:form 'required_if_any'}
  // {input:field type='text', 'address', label='Address', rules='required_if_any:delivery,ship;plan,pro'/}
  // <button type='submit'>Validate</button>
  // {/input:form}
  // ```
  //
  // Run the focused fixture with `php tests/subsystems/rule-required_if_any.php`.

$test=fn($value,$arg,$data)=>padRuleFilled($value)||!array_filter(explode(';',$arg),fn($part)=>padRuleSiblingIs($data,$part));
return ['empty'=>TRUE,'check'=>$test,'list'=>$test,
 'argument'=>fn($arg)=>$arg!==''&&count(explode(';',$arg))<=20&&!array_filter(explode(';',$arg),fn($p)=>padRuleFieldValues($p)[0]===''||!padRuleFieldValues($p)[1])?'':'use field,value;other,value, at most 20 conditions',
 'fields'=>fn($arg)=>array_map(fn($p)=>padRuleFieldValues($p)[0],explode(';',$arg)),
 'shown'=>fn($arg)=>implode(' or ',array_map('padRuleShownIs',explode(';',$arg))), 'message'=>':label is required when :n'];
