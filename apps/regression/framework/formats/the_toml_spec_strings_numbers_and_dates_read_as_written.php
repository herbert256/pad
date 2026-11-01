<?php

  // The examples of the TOML 1.0 specification (toml.io/en/v1.0.0) for strings, integers,
  // floats, booleans and dates, each read and shown as JSON: the three ways to write the
  // quick brown fox are one text, the escapes and the multi-line forms keep what the
  // specification says they keep, 0xDEADBEEF is 3735928559, and a date is the text it is -
  // the space between date and time read as a T.

  $tomlStrings = <<<'TOML'
str1 = "The quick brown fox jumps over the lazy dog."
str2 = """
The quick brown \


  fox jumps over \
    the lazy dog."""
str3 = """\
       The quick brown \
       fox jumps over \
       the lazy dog.\
       """
str4 = "I'm a string. \"You can quote me\". Name\tJos\u00E9\nLocation\tSF."
winpath2 = '\\ServerX\admin$\system32\'
regex2 = '''I [dw]on't need \d{2} apples'''
lines  = '''
The first newline is
trimmed in raw strings.
'''
quot15 = '''Here are fifteen quotation marks: """""""""""""""'''
str = ''''That,' she said, 'is still pointless.''''
str7 = """"This," she said, "is just a pointless statement.""""
TOML;

  $tomlNumbers = <<<'TOML'
int1 = +99
int4 = -17
int6 = 5_349_221
hex1 = 0xDEADBEEF
oct1 = 0o01234567
bin1 = 0b11010110
max = 9223372036854775807
flt1 = +1.0
flt4 = 5e+22
flt6 = -2E-2
flt8 = 224_617.445_991_228
bool1 = true
odt1 = 1979-05-27T07:32:00Z
odt3 = 1979-05-27T00:32:00.999999-07:00
odt4 = 1979-05-27 07:32:00Z
ldt1 = 1979-05-27T07:32:00
ld1 = 1979-05-27
lt2 = 00:32:00.999999
TOML;

  $tomlRead = padFormatRead ( 'toml', $tomlStrings );

  $tomlFox  = ( $tomlRead ['str1'] === $tomlRead ['str2'] and $tomlRead ['str1'] === $tomlRead ['str3'] ) ? 'one fox' : 'three foxes';
  $tomlText = json_encode ( array_diff_key ( $tomlRead, [ 'str2' => 1, 'str3' => 1 ] ), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES );

  $tomlKinds = [];

  foreach ( padFormatRead ( 'toml', $tomlNumbers ) as $key => $value )
    $tomlKinds [] = $key . '=' . gettype ( $value ) . ':' . var_export ( $value, TRUE );

  $tomlNumbers = implode ( ' ', $tomlKinds );

?>
