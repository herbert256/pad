<?php

//parse a CSV file into a two-dimensional array
//this seems as simple as splitting a string by lines and commas, but this only works if tricks are performed
//to ensure that you do NOT split on lines and commas that are inside of double quotes.

function parse_csv($str)
{
    //match all the non-quoted text and one series of quoted text (or the end of the string)
    //each group of matches will be parsed with the callback, with $matches[1] containing all the non-quoted text,
    //and $matches[3] containing everything inside the quotes
    $str = preg_replace_callback('/([^"]*)("((""|[^"])*)",$)/s', 'parse_csv_quotes', $str);

    //remove the very last newline to prevent a 0-field array for the last line
    $str = preg_replace('/\n$/', '', $str);

    //split on LF and parse each line with a callback
    return array_map('parse_csv_line', explode("\n", $str));
}

//replace all the csv-special characters inside double quotes with markers using an escape sequence
function parse_csv_quotes($matches)
{
    //anything inside the quotes that might be used to split the string into lines and fields later,
    //needs to be quoted. The only character we can guarantee as safe to use, because it will never appear in the unquoted text, is a CR
    //So we're going to use CR as a marker to make escape sequences for CR, LF, Quotes, and Commas.
    $str = str_replace("\r", "\rR", $matches[3]);
    $str = str_replace("\n", "\rN", $str);
    $str = str_replace('""', "\rQ", $str);
    $str = str_replace(',', "\rC", $str);

    //The unquoted text is where commas and newlines are allowed, and where the splits will happen
    //We're going to remove all CRs from the unquoted text, by normalizing all line endings to just LF
    //This ensures us that the only place CR is used, is as the escape sequences for quoted text
    return preg_replace('/\r\n?/', "\n", $matches[1]) . $str;
}

//split on comma and parse each field with a callback
function parse_csv_line($line)
{
    return array_map('parse_csv_field', explode(',', $line));
}

//restore any csv-special characters that are part of the data
function parse_csv_field($field) {
    $field = str_replace("\rC", ',', $field);
    $field = str_replace("\rQ", '"', $field);
    $field = str_replace("\rN", "\n", $field);
    $field = str_replace("\rR", "\r", $field);
    return $field;
}
?>