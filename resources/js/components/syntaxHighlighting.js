import hljs from 'highlight.js';
import hljsAntlers from 'highlightjs-antlers';
import hljsBlade from 'highlightjs-blade';

hljs.registerLanguage('antlers', hljsAntlers);
hljs.registerLanguage('blade', hljsBlade);

export function initSyntaxHighlighting() {
    hljs.highlightAll();
}
