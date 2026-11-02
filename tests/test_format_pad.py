"""Source-layout contracts: preserve the content that PAD and HTML interpret."""

import importlib.util
from pathlib import Path
import sys
import unittest


spec = importlib.util.spec_from_file_location('pad_formatter', Path(__file__).resolve().parents[1] / 'scripts/format-pad.py')
formatter = importlib.util.module_from_spec(spec)
sys.modules[spec.name] = formatter
spec.loader.exec_module(formatter)


class FormatPad(unittest.TestCase):
    def format(self, source):
        result, _ = formatter.format_source(source)
        self.assertEqual(formatter.signature(source), formatter.signature(result))
        self.assertEqual(formatter.format_source(result)[0], result)
        return result

    def test_nested_html_and_pad(self):
        source = '<ul>{array:items}<li><p>{$name}</p></li>@else@<li>Empty</li>{/array:items}</ul>'
        result = self.format(source)
        self.assertIn('  {array:items}\n    <li>\n      <p>{$name}</p>', result)
        self.assertIn('  @else@\n    <li>Empty</li>', result)

    def test_inline_output_has_no_added_spaces(self):
        source = '<p>Hello <b>{$name}</b>!<strong>{pad:if $n gt 0}+{/pad:if}{$n}</strong></p>'
        self.assertEqual(self.format(source), source + '\n')

    def test_attribute_conditionals_and_nested_quotes(self):
        source = '<table><tr{pad:if $done} class="done"{/pad:if}><td><a href="?id={$id}">{$name}</a></td></tr></table>'
        self.assertIn('<tr{pad:if $done} class="done"{/pad:if}>', self.format(source))

    def test_multiline_inline_layout_is_reindented(self):
        source = '<section><footer>\n <span>A</span>\n <span>B</span>\n</footer></section>'
        self.assertIn('  <footer>\n    <span>A</span>\n    <span>B</span>\n  </footer>', self.format(source))

    def test_pre_and_textarea_are_exact(self):
        for tag in ('pre', 'textarea', 'script', 'style'):
            raw = f'<{tag}>\n  a < b; {{ x: 1 }}\n\n</{tag}>'
            self.assertIn(raw, self.format('<div>' + raw + '</div>'))

    def test_data_and_highlight_bodies_are_exact(self):
        for tag in ('format:auto', 'chart:bar', 'notation:abc', 'widget:highlight', 'pad:ignore', 'pad:content'):
            source = '{' + tag + " 'example'}\n  one: two\n    three: '<tag>'\n{/" + tag + '}'
            self.assertEqual(self.format(source), source)

    def test_long_arguments_keep_literal_values(self):
        source = "{input:field 'title', label='A long title that includes, a comma', placeholder='Please enter a descriptive title for this record', rules='required|max:120'/}"
        result = self.format(source)
        self.assertIn("\n  label='A long title that includes, a comma',", result)
        self.assertIn("rules='required|max:120'", result)

    def test_metadata_stays_on_one_line_for_source_galleries(self):
        source = "{pad:meta title='Example',\n  about='A source gallery reads this metadata.'\n/}"
        self.assertEqual(self.format(source), "{pad:meta title='Example', about='A source gallery reads this metadata.'/}")

    def test_conditional_html_delimiters(self):
        source = '{array:rows}{property:first@rows}<ul>{/property:first@rows}<li>{$name}</li>{property:last@rows}</ul>{/property:last@rows}{/array:rows}'
        self.assertIn('    <ul>', self.format(source))

    def test_optional_html_closings(self):
        self.format('<ul>\n<li>First\n<li>Second\n</ul>')
        self.format("<center><p>{common:example 'hello/hello', skipNames/}</center>")

    def test_printed_braces_stay_next_to_their_text(self):
        source = '<p>Example({pad:open/}^value{pad:close/})</p>'
        self.assertEqual(self.format(source), source + '\n')
        source = '<p>Use <code>{pad:ignore}{barcode:qr}{/pad:ignore}</code>.</p>'
        self.assertEqual(self.format(source), source + '\n')

    def test_game_clues_are_data_even_when_they_contain_html(self):
        source = '{game:crossword paper}\n  PHP: The language <PAD> is written in\n{/game:crossword}'
        self.assertEqual(self.format(source), source)

    def test_case_when_is_a_branch(self):
        source = "{pad:case $kind}{pad:when 'one'}<p>One</p>@else@<p>Other</p>{/pad:case}"
        self.assertIn("{pad:when 'one'}\n  <p>One</p>", self.format(source))

    def test_text_only_output_separators_are_exact(self):
        for source in ('{sequence:range 1,3}{$value}{/sequence:range}', '{$a} {$b}\n', 'plain text'):
            self.assertEqual(self.format(source), source)

    def test_whitespace_controls_and_encoding_examples_are_exact(self):
        for source in ('<p>{~data:rows~} {$name} {~/data:rows~}</p>', '\ufeff<p>A</p>', '<p>A</p>\r\n'):
            self.assertEqual(formatter.format_source(source)[0], source)

    def test_unclosed_literals_are_not_repaired(self):
        for source in ('{pad:echo "oops/}', '<pre>unfinished', '{pad:ignore}unfinished'):
            self.assertEqual(formatter.format_source(source)[0], source)


if __name__ == '__main__':
    unittest.main()
