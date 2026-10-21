/**
 * Vyrobí z assets/css/main.css variantu pro editor: assets/css/editor.css
 *
 * Proč: Tailwind v4 dává styly do kaskádových vrstev (@layer). Styly editoru
 * WordPressu žádnou vrstvu nemají a nezařazené CSS vždy přebije to ve vrstvě –
 * v náhledu bloků by tak vyhrály nadpisy a seznamy z editoru. Odstraněním
 * obalů @layer (obsah zůstává ve stejném pořadí) má náhled stejný vzhled jako web.
 */

const fs = require('fs');
const path = require('path');

const root = path.join(__dirname, '..');
const src = path.join(root, 'assets/css/main.css');
const dest = path.join(root, 'assets/css/editor.css');

const css = fs.readFileSync(src, 'utf8');
let out = '';
let i = 0;

while (i < css.length) {
	// Deklarace pořadí vrstev, např. "@layer theme,base,components,utilities;"
	const decl = /^@layer\s+[^{;]*;/.exec(css.slice(i));
	if (decl) {
		i += decl[0].length;
		continue;
	}

	// Obal vrstvy: "@layer name {" … "}" – zahodíme obal, obsah necháme.
	const open = /^@layer\s+[^{;]*\{/.exec(css.slice(i));
	if (open) {
		const start = i + open[0].length;
		let depth = 1;
		let j = start;
		while (j < css.length && depth > 0) {
			const c = css[j];
			if (c === '{') depth++;
			else if (c === '}') depth--;
			j++;
		}
		out += css.slice(start, j - 1);
		i = j;
		continue;
	}

	out += css[i];
	i++;
}

fs.writeFileSync(dest, out);
console.log(
	`editor.css: ${(out.length / 1024).toFixed(1)} kB (z main.css ${(css.length / 1024).toFixed(1)} kB), @layer: ${
		/@layer/.test(out) ? 'ZŮSTALY' : 'odstraněny'
	}`
);
