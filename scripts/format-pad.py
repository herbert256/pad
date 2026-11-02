#!/usr/bin/env python3
"""Format teaching templates without rewriting literal data or inline output.

Use --check in a review, or --write to apply the same two-space layout. Raw bodies,
inline runs and deliberately malformed/whitespace-sensitive fixtures stay intact.
This is a conservative source formatter, not an HTML repair tool or PAD compiler.
"""

import argparse
from collections import Counter
from dataclasses import dataclass, field
import json
from pathlib import Path
import re


ROOT = Path(__file__).resolve().parents[1]
VOID = set('area base br col embed hr img input link meta param source track wbr'.split())
INLINE = set('a abbr b bdi bdo br button cite code data del dfn em i img input ins kbd label mark meter output q ruby s samp small span strong sub sup time u var wbr'.split())
RAW_HTML = {'pre', 'textarea', 'script', 'style', 'xmp'}
RAW_PAD = {'pad:ignore', 'pad:content', 'pad:raw', 'pad:php', 'pad:markdown', 'pad:comment', 'pad:spaceless', 'widget:highlight', 'widget:markdown'}
RAW_PREFIX = {'format', 'notation', 'chart', 'sheet', 'diagram', 'map', 'timeline', 'jsonld', 'sound', 'search', 'game', 'ai', 'printable'}
BRANCH = {'pad:else', 'pad:elseif', 'pad:when', 'pad:default'}
PAD_START = re.compile(r'\{~?(?:/?[A-Za-z_]\w*:[\w$@-]+|[$!?#&^%])')
PAD_NAME = re.compile(r'\{~?(/?)([A-Za-z_]\w*:[\w$@-]+)')
HTML_START = re.compile(r'</?[A-Za-z][\w:-]*(?=[\s/>{])')
MARKER = re.compile(r'@(page|content|start|end|else|tidy)@')


class Preserve(ValueError):
    """Input with an ambiguous structure must remain as written."""


@dataclass
class Node:
    kind: str
    name: str
    opening: str
    closing: str = ''
    children: list = field(default_factory=list)

    def raw(self):
        return self.opening + ''.join(child.raw() for child in self.children) + self.closing


def quote_end(source, start):
    quote, i = source[start], start + 1
    while i < len(source):
        if source[i] == '\\':
            i += 2
        elif source[i] == quote:
            return i + 1
        else:
            i += 1
    raise Preserve('unclosed quoted value')


def pad_end(source, start):
    i = start + 1
    while i < len(source):
        if source[i] in "\"'":
            i = quote_end(source, i)
        elif source[i] == '{':
            i = pad_end(source, i)
        elif source[i] == '}':
            return i + 1
        else:
            i += 1
    raise Preserve('unclosed PAD tag')


def html_end(source, start):
    quote, i = '', start + 1
    while i < len(source):
        char = source[i]
        if PAD_START.match(source, i):
            i = pad_end(source, i)
            continue
        if quote:
            if char == quote:
                quote = ''
        elif char in "\"'":
            quote = char
        elif char == '>':
            return i + 1
        i += 1
    raise Preserve('unclosed HTML tag')


def delimited_end(source, start, closing):
    end = source.find(closing, start)
    if end < 0:
        raise Preserve('unclosed literal block')
    return end + len(closing)


def tokens(source):
    """Keep attributes, quoted values and opaque bodies as indivisible tokens."""
    i, start = 0, 0
    while i < len(source):
        kind, name, end = '', '', i
        if source.startswith('<!--', i):
            kind, end = 'literal', delimited_end(source, i + 4, '-->')
        elif source.startswith('{--', i) and source[i+3:i+4].isspace():
            kind, end = 'literal', delimited_end(source, i + 3, '--}')
        elif source.startswith('{#', i) and re.match(r'\{#\s', source[i:]):
            kind, end = 'literal', delimited_end(source, i + 2, '#}')
        elif source.startswith('<!', i) or source.startswith('<?', i):
            kind, end = 'literal', html_end(source, i)
        elif HTML_START.match(source, i):
            end = html_end(source, i)
            name = re.match(r'</?([\w:-]+)', source[i:end])[1].lower()
            kind = 'end' if source[i+1] == '/' else 'atom' if name in VOID or source[i:end].endswith('/>') else 'html'
            if kind == 'html' and name in RAW_HTML:
                close = re.search(r'</' + name + r'\s*>', source[end:], re.I)
                if close is None:
                    raise Preserve('unclosed raw HTML body')
                end += close.end()
                kind = 'literal'
        elif PAD_START.match(source, i):
            end = pad_end(source, i)
            raw = source[i:end]
            match = PAD_NAME.match(raw)
            if match:
                name = match[2]
                kind = 'end' if match[1] else 'branch' if name in BRANCH else 'atom' if re.search(r'/~?\}$', raw) else 'pad'
                if kind == 'pad' and (name in RAW_PAD or name.split(':')[0] in RAW_PREFIX or re.search(r'\b(?:ignore|literal)\b', raw[len(match[0]):])):
                    close = re.search(r'\{~?/' + re.escape(name) + r'(?:\s[^{}]*)?~?\}', source[end:])
                    if close is None:
                        raise Preserve('unclosed literal PAD body')
                    body_end = end + close.start()
                    final = end + close.end()
                    if i > start:
                        yield ('text', '', source[start:i])
                    yield ('raw-open', name, source[i:end])
                    yield ('raw-body', name, source[end:body_end])
                    yield ('raw-end', name, source[body_end:final])
                    i = start = final
                    continue
            else:
                kind = 'value'
        elif MARKER.match(source, i):
            marker = MARKER.match(source, i)
            end = marker.end()
            kind = 'branch' if marker[1] == 'else' else 'value'
        if kind:
            if i > start:
                yield ('text', '', source[start:i])
            yield (kind, name, source[i:end])
            i = start = end
        else:
            i += 1
    if start < len(source):
        yield ('text', '', source[start:])


def parse(source):
    root = Node('root', '', '')
    stack = [root]
    source_tokens = list(tokens(source))
    closes = {name for kind, name, _ in source_tokens if kind in ('end', 'raw-end')}
    for kind, name, raw in source_tokens:
        if kind == 'pad' and name not in closes:
            kind = 'atom'
        if kind in ('end', 'raw-end'):
            if stack[-1].name != name:
                # HTML permits omitted li/dt/dd/option and paragraph closing tags.
                while len(stack) > 1 and stack[-1].kind == 'html' and stack[-1].name in {'li', 'dt', 'dd', 'option', 'p'} and stack[-1].name != name:
                    stack.pop()
            if stack[-1].name != name:
                if raw.startswith('</'):
                    # A conditional may open HTML in one PAD block and close it
                    # in another, and _inits/_exits may split an HTML document.
                    stack[-1].children.append(Node('literal', '', raw))
                    continue
                found = next((i for i in range(len(stack)-1, 0, -1) if stack[i].name == name), None)
                if found is not None and all(node.kind == 'html' for node in stack[found+1:]):
                    del stack[found+1:]
                else:
                    raise Preserve('crossing or unmatched block tags')
            stack.pop().closing = raw
        else:
            if kind == 'html' and stack[-1].kind == 'html' and name in {'li', 'dt', 'dd', 'option'} and stack[-1].name in ({'dt', 'dd'} if name in {'dt', 'dd'} else {name}):
                stack.pop()
            node = Node(kind, name, raw)
            stack[-1].children.append(node)
            if kind in ('html', 'pad', 'raw-open'):
                stack.append(node)
    if any(node.kind in ('pad', 'raw-open') for node in stack):
        raise Preserve('unclosed PAD block')
    return root


def arguments(source):
    parts, depth, start, i = [], 0, 0, 0
    while i < len(source):
        char = source[i]
        if char in "\"'":
            i = quote_end(source, i)
            continue
        if char in '[({':
            depth += 1
        elif char in '])}':
            depth -= 1
        elif char == ',' and depth == 0:
            parts.append(source[start:i].strip())
            start = i + 1
        i += 1
    parts.append(source[start:].strip())
    return parts


def tag_layout(raw, depth):
    indent = '  ' * depth
    match = re.fullmatch(r'(\{[\w]+:[\w$@-]+)\s+([\s\S]*?)(/\}|\})', raw)
    if not match:
        return indent + raw
    tag, body, closing = match.groups()
    body = body.strip()
    if tag == '{pad:meta':
        # The example galleries extract page metadata from one source line.
        return indent + tag + ' ' + ', '.join(arguments(body)) + closing
    if tag == '{pad:set' and body.startswith('$frameMenu = ['):
        rows = arguments(body[len('$frameMenu = ['):-1])
        return '\n'.join([indent + tag + ' $frameMenu = ['] + [indent + '  ' + row + (',' if i < len(rows)-1 else '') for i, row in enumerate(rows)] + [indent + ']' + closing])
    if tag == '{pad:set' and len(raw) + len(indent) > 120:
        db = re.fullmatch(r'(\$\w+ = php:db)\(([\s\S]*)\)', body)
        if db:
            args = arguments(db[2])
            if len(args) == 2 and args[1].startswith('['):
                values = arguments(args[1][1:-1])
                return '\n'.join([indent + tag + ' ' + db[1] + '(', indent + '  ' + args[0] + ',', indent + '  ['] + [indent + '    ' + value + (',' if i < len(values)-1 else '') for i, value in enumerate(values)] + [indent + '  ]', indent + ')' + closing])
        assignments = arguments(body)
        if len(assignments) > 1:
            return '\n'.join([indent + tag] + [indent + '  ' + item + (',' if i < len(assignments)-1 else '') for i, item in enumerate(assignments)] + [indent + closing])
    args = arguments(body)
    if ('\n' in raw or len(raw) + len(indent) > 120) and len(args) > 1:
        return '\n'.join([indent + tag + ' ' + args[0] + ','] + [indent + '  ' + item + (',' if i < len(args)-2 else '') for i, item in enumerate(args[1:])] + [indent + closing])
    return indent + tag + ' ' + body + closing


def inline(node):
    if node.kind in {'text', 'value', 'branch'}:
        return True
    if node.kind == 'atom':
        return node.name in INLINE or (':' in node.name and node.name not in {'pad:set', 'pad:meta', 'pad:parms', 'pad:redirect', 'pad:asset'} and not node.name.startswith('input:'))
    if node.kind == 'raw-open':
        return '\n' not in node.raw()
    if node.kind == 'html' and node.name in INLINE:
        return compact(node)
    if node.kind == 'pad':
        return compact(node)
    return False


def compact(node):
    if node.kind == 'html' and not node.closing and any(child.kind != 'text' for child in node.children):
        return False
    return all(inline(child) for child in node.children) and not any(child.kind == 'text' and not child.opening.strip() and '\n' in child.opening for child in node.children)


def render_children(node, depth):
    lines, run = [], []

    def flush():
        if run:
            raw = ''.join(child.raw() for child in run)
            if raw.strip():
                lines.append('  ' * depth + raw.strip())
            elif '\n\n' in raw and lines and lines[-1] != '':
                lines.append('')
            run.clear()

    for child in node.children:
        if child.kind == 'text' and not child.opening.strip() and '\n' in child.opening:
            flush()
            if '\n\n' in child.opening and lines and lines[-1] != '':
                lines.append('')
        elif inline(child) and child.kind != 'branch':
            # Keep inline neighbours together: inserting a space can change a label,
            # a generated filename, a signed value or a sequence of numbers.
            run.append(child)
        else:
            flush()
            lines.append(render(child, depth))
    flush()
    return '\n'.join(lines).strip('\n')


def render(node, depth=0):
    indent = '  ' * depth
    if node.kind == 'root':
        return render_children(node, depth)
    if node.kind == 'branch':
        return tag_layout(node.raw(), max(0, depth-1))
    if node.kind in {'text', 'value', 'literal', 'raw-body'}:
        return indent + node.raw()
    if node.kind == 'atom':
        return tag_layout(node.raw(), depth)
    if node.kind == 'raw-open':
        # Neither the raw body nor the bytes separating it from its delimiters move.
        return tag_layout(node.opening, depth) + ''.join(child.raw() for child in node.children) + node.closing
    if compact(node):
        result = tag_layout(node.opening, depth) + ''.join(child.raw() for child in node.children) + node.closing
        return result.rstrip() if node.kind == 'html' and not node.closing else result
    children = render_children(node, depth+1)
    result = tag_layout(node.opening, depth)
    if children:
        result += '\n' + children
    if node.closing:
        result += '\n' + indent + node.closing
    return result


def unquoted(source):
    result, i = [], 0
    while i < len(source):
        if source[i] in "\"'":
            end = quote_end(source, i)
            result.append(source[i:end])
            i = end
        else:
            if not source[i].isspace():
                result.append(source[i])
            i += 1
    return ''.join(result)


def signature(source):
    return [(kind, name, raw if kind in {'literal', 'raw-body'} else raw.strip() if kind == 'text' else unquoted(raw)) for kind, name, raw in tokens(source) if raw.strip() or kind == 'raw-body']


def format_source(source):
    if '\r' in source or '\ufeff' in source or re.search(r'\{~|~\}', source):
        return source, 'explicit whitespace or encoding example'
    try:
        tree = parse(source)
        # Whitespace between text-only PAD tags is output. Only their argument lists
        # can be reformatted safely, with the original separators left untouched.
        has_html = any(kind == 'html' for kind, _, _ in tokens(source))
        if not has_html:
            result = ''.join(tag_layout(raw, 0) if kind in {'atom', 'pad', 'raw-open', 'branch'} else raw for kind, _, raw in tokens(source))
        else:
            result = render(tree).strip('\n') + '\n'
        if signature(source) != signature(result):
            raise Preserve('literal or token boundary would change')
        return result, 'formatted' if source != result else 'already readable'
    except Preserve as error:
        return source, str(error)


def main():
    parser = argparse.ArgumentParser(description=__doc__)
    mode = parser.add_mutually_exclusive_group()
    mode.add_argument('--write', action='store_true', help='write formatted sources')
    mode.add_argument('--check', action='store_true', help='exit 1 when formatting is needed')
    parser.add_argument('--report', type=Path, help='write a JSON review of every file')
    parser.add_argument('paths', nargs='*', type=Path, default=[ROOT/'apps'])
    args = parser.parse_args()
    files = sorted({file for path in args.paths for file in (path.rglob('*.pad') if path.is_dir() else [path])})
    report = []
    for path in files:
        source = path.read_bytes().decode('utf-8')
        # These fixtures deliberately assert their exact input, including invalid
        # syntax. Formatting them would change the thing the suite is testing.
        parts = path.parts
        if ('regression' in parts and parts[parts.index('regression') + 1] != 'main') or any(part in {'whitespace', 'trimming'} for part in parts):
            result, reason = source, 'exact-source fixture'
        else:
            result, reason = format_source(source)
        changed = result != source
        if changed and args.write:
            path.write_bytes(result.encode('utf-8'))
        report.append({'path': str(path), 'changed': changed, 'reason': reason})
    if args.report:
        args.report.write_text(json.dumps(report, indent=2) + '\n')
    count = sum(item['changed'] for item in report)
    print(f'{len(files)} PAD files reviewed; {count} ' + ('formatted.' if args.write else 'would change.'))
    for reason, total in sorted(Counter(item['reason'] for item in report).items()):
        print(f'  {total}: {reason}')
    return int(args.check and count > 0)


if __name__ == '__main__':
    raise SystemExit(main())
