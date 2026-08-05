import re

import sublime
import sublime_plugin

# {tag or {/tag, with optional type prefix ({data:items})
TAG_RE = re.compile(r'\{(/?)([A-Za-z_][A-Za-z0-9_]*(?::[A-Za-z_][A-Za-z0-9_]*)?)')

# built-in tags that never take a closing tag - kept off the open-tag stack
SINGLE_TAGS = {
    'set', 'get', 'echo', 'increment', 'decrement', 'redirect', 'restart',
    'exit', 'break', 'continue', 'cease', 'dump', 'error', 'exception',
    'open', 'close', 'null', 'true', 'false', 'flag', 'page', 'curl',
    'exists', 'at', 'resume', 'switch', 'ajax', 'reactData', 'file',
    'make', 'keep', 'remove', 'action',
}


def open_tags(view, point):
    """Stack of PAD tags opened but not closed before point, innermost last."""
    text = view.substr(sublime.Region(0, point))
    stack = []
    for m in TAG_RE.finditer(text):
        closing, name = m.group(1), m.group(2)
        if closing:
            if name in stack:
                del stack[len(stack) - 1 - stack[::-1].index(name):]
        elif name not in SINGLE_TAGS:
            stack.append(name)
    return stack


class PadCloseTagCommand(sublime_plugin.TextCommand):
    """Insert the closing tag for the innermost open PAD tag.

    Falls back to Sublime's HTML close_tag when no PAD tag is open.
    """

    def run(self, edit):
        view = self.view
        point = view.sel()[0].b
        stack = open_tags(view, point)
        if stack:
            view.insert(edit, point, '{/' + stack[-1] + '}')
        else:
            view.run_command('close_tag')

    def is_enabled(self):
        return self.view.match_selector(0, 'text.html.pad')


class PadCloseTagListener(sublime_plugin.EventListener):
    """After typing {/ offer the currently open PAD tags as completions."""

    def on_query_completions(self, view, prefix, locations):
        point = locations[0] - len(prefix)
        if point < 2:
            return None
        if not view.match_selector(point, 'text.html.pad'):
            return None
        if view.substr(sublime.Region(point - 2, point)) != '{/':
            return None
        stack = open_tags(view, point - 2)
        if not stack:
            return None
        items = [
            sublime.CompletionItem(
                trigger=name,
                annotation='close tag',
                completion=name + '}',
                kind=(sublime.KIND_ID_MARKUP, '/', 'Close'),
            )
            for name in reversed(stack)
        ]
        return sublime.CompletionList(
            items,
            sublime.INHIBIT_WORD_COMPLETIONS | sublime.INHIBIT_EXPLICIT_COMPLETIONS,
        )
