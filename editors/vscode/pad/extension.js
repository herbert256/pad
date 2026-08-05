const vscode = require('vscode');
const COMPLETIONS = require('./completions.json');

// {tag or {/tag, with optional type prefix ({data:items})
const TAG_RE = /\{(\/?)([A-Za-z_][A-Za-z0-9_]*(?::[A-Za-z_][A-Za-z0-9_]*)?)/g;

// built-in tags that never take a closing tag
const SINGLE_TAGS = new Set([
    'set', 'get', 'echo', 'increment', 'decrement', 'redirect', 'restart',
    'exit', 'break', 'continue', 'cease', 'dump', 'error', 'exception',
    'open', 'close', 'null', 'true', 'false', 'flag', 'page', 'curl',
    'exists', 'at', 'resume', 'switch', 'ajax', 'reactData', 'file',
    'make', 'keep', 'remove', 'action',
]);

function openTags(document, position) {
    const text = document.getText(new vscode.Range(new vscode.Position(0, 0), position));
    const stack = [];
    let m;
    TAG_RE.lastIndex = 0;
    while ((m = TAG_RE.exec(text)) !== null) {
        const closing = m[1], name = m[2];
        if (closing) {
            const i = stack.lastIndexOf(name);
            if (i >= 0) stack.length = i;
        } else if (!SINGLE_TAGS.has(name)) {
            stack.push(name);
        }
    }
    return stack;
}

function insidePadTag(document, position) {
    const from = position.line > 50
        ? new vscode.Position(position.line - 50, 0)
        : new vscode.Position(0, 0);
    const text = document.getText(new vscode.Range(from, position));
    const open = text.lastIndexOf('{');
    if (open < 0) return false;
    if (text.indexOf('}', open) >= 0) return false;
    return /[/A-Za-z_$%!@]/.test(text.charAt(open + 1) || '');
}

function activate(context) {
    // completion of open tags after typing {/
    context.subscriptions.push(vscode.languages.registerCompletionItemProvider(
        { language: 'pad' },
        {
            provideCompletionItems(document, position) {
                const line = document.lineAt(position.line).text.slice(0, position.character);
                const m = line.match(/\{\/([A-Za-z0-9_:]*)$/);
                if (m) {
                    const stack = openTags(document,
                        position.translate(0, -(m[0].length)));
                    return stack.reverse().map((name, i) => {
                        const item = new vscode.CompletionItem(
                            name, vscode.CompletionItemKind.Snippet);
                        item.insertText = name + '}';
                        item.detail = 'close tag';
                        item.sortText = String(i).padStart(3, '0');
                        return item;
                    });
                }
                if (!insidePadTag(document, position)) return undefined;
                return COMPLETIONS.map((c) => {
                    const item = new vscode.CompletionItem(
                        c.label, vscode.CompletionItemKind[c.kind]);
                    if (c.insert) item.insertText = c.insert;
                    item.detail = c.detail;
                    return item;
                });
            },
        },
        '/', ':', '$'
    ));

    // PAD: Close Tag (alt+cmd+.)
    context.subscriptions.push(vscode.commands.registerCommand('pad.closeTag', () => {
        const editor = vscode.window.activeTextEditor;
        if (!editor || editor.document.languageId !== 'pad') return;
        const position = editor.selection.active;
        const stack = openTags(editor.document, position);
        if (!stack.length) return;
        editor.edit((edit) => {
            edit.insert(position, '{/' + stack[stack.length - 1] + '}');
        });
    }));
}

function deactivate() {}

module.exports = { activate, deactivate };
