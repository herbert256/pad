const vscode = require('vscode');
const COMPLETIONS = require('./completions.json');

// {function:tag or {/tag, with optional type prefix ({data:items}), and the ~ of whitespace control
// just inside the brace ({~items~}, {~/items})
const TAG_RE = /\{~?(\/?)([A-Za-z_][A-Za-z0-9_]*:[A-Za-z_][A-Za-z0-9_]*(?:@[A-Za-z0-9_-]*)?)([^{}]*)/g;

// The branch words of {pad:if} and {pad:case} divide a pair; they never open one of their own.
const BRANCH_TAGS = new Set(['pad:else', 'pad:elseif', 'pad:when']);

// The comments the engine drops before it scans - {# ... #}, not the option sigil {#name},
// and {-- ... --} - with any tag written inside them.
function stripComments(text) {
    return text
        .replace(/\{#(?![A-Za-z_][A-Za-z0-9_]*\s*[}|])[\s\S]*?#\}/g, '')
        .replace(/\{--\s[\s\S]*?--\}/g, '');
}

// The pairs still open before the position. A tag written without the slash opens a pair - a
// single tag is written {function:tag/}, or {function:tag ... /| pipe} - so no list of single tags is needed.

function openTags(document, position) {
    const text = stripComments(document.getText(new vscode.Range(new vscode.Position(0, 0), position)));
    const stack = [];
    let m;
    TAG_RE.lastIndex = 0;
    while ((m = TAG_RE.exec(text)) !== null) {
        const closing = m[1], name = m[2];
        if (closing) {
            const i = stack.lastIndexOf(name);
            if (i >= 0) stack.length = i;
        } else if (!BRANCH_TAGS.has(name) && !m[3].replace(/~$/, '').endsWith('/') && !/\/\s*\|/.test(m[3])) {
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
    // past the ~ of whitespace control - {~ec - as past the brace itself
    const first = text.charAt(open + 1) === '~' ? text.charAt(open + 2) : text.charAt(open + 1);
    return !first || /[/A-Za-z_$%!@?#&^]/.test(first);
}

function activate(context) {
    // completion of open tags after typing {/
    context.subscriptions.push(vscode.languages.registerCompletionItemProvider(
        { language: 'pad' },
        {
            provideCompletionItems(document, position) {
                const line = document.lineAt(position.line).text.slice(0, position.character);
                const m = line.match(/\{~?\/([A-Za-z0-9_:]*)$/);
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
                const head = line.slice(line.lastIndexOf('{') + 1).replace(/^~/, '');
                const tagPosition = /^[A-Za-z_0-9:]*$/.test(head);
                const prefix = tagPosition && head.includes(':') ? head.split(':')[0] + ':' : '';
                const candidates = tagPosition ? COMPLETIONS.flatMap((c) => {
                    if (c.label.includes(':')) return [c];
                    if (c.detail === 'pipe function') return [{ ...c, label: 'function:' + c.label }];
                    if (c.detail === 'property@tag') return [{ ...c, label: 'property:' + c.label }];
                    return [];
                }).filter((c) => !prefix || c.label.startsWith(prefix)) : COMPLETIONS;
                return candidates.map((c) => {
                    const item = new vscode.CompletionItem(
                        c.label, vscode.CompletionItemKind[c.kind]);
                    item.insertText = tagPosition ? c.label.slice(prefix.length) : (c.insert || c.label);
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
