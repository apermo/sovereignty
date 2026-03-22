#!/usr/bin/env node

/**
 * Build script: compiles SASS, converts indentation to tabs,
 * replaces @@placeholders in style.css, and generates minified CSS.
 */

const { execSync } = require('child_process');
const fs = require('fs');
const path = require('path');

const root = path.resolve(__dirname, '..');
const pkg = require(path.join(root, 'package.json'));
const composer = require(path.join(root, 'composer.json'));

// Resolve primary author from authors[] array.
const author = pkg.authors?.[0] || pkg.author || {};
const repoUrl = (pkg.repository?.url || '').replace(/\.git$/, '');
const githubSlug = repoUrl.replace('https://github.com/', '');
const requiresPhp = (composer.require?.php || '').replace(/^[>=^~]+/, '');

// SASS compilation map: output → source.
const sassFiles = {
	'style.css': 'assets/sass/style.scss',
	'assets/css/editor-style.css': 'assets/sass/editor-style.scss',
	'assets/css/print.css': 'assets/sass/print.scss',
	'assets/css/narrow-width.css': 'assets/sass/responsive_narrow.scss',
	'assets/css/default-width.css': 'assets/sass/responsive_default.scss',
	'assets/css/wide-width.css': 'assets/sass/responsive_wide.scss',
};

// String replacements for style.css header.
const replacements = {
	'@@author': author.name || '',
	'@@author_url': author.url || '',
	'@@version': pkg.version || '',
	'@@license': pkg.license || '',
	'@@license_url': `https://opensource.org/licenses/${pkg.license || ''}`,
	'@@name': pkg.name || '',
	'@@description': pkg.description || '',
	'@@homepage': pkg.homepage || '',
	'@@requires_php': requiresPhp,
	'@@tested_up_to': pkg.testedUpTo || '',
	'@@github_slug': githubSlug,
	'@@github_url': repoUrl,
	'@@tags': (pkg.keywords || []).join(', '),
};

// Step 1: Compile SASS (expanded + source maps).
console.log('Compiling SASS...');
for (const [output, source] of Object.entries(sassFiles)) {
	const outputPath = path.join(root, output);
	const sourcePath = path.join(root, source);
	execSync(
		`npx sass --no-error-css --style=expanded --source-map "${sourcePath}:${outputPath}"`,
		{ cwd: root, stdio: 'inherit' }
	);
}

// Step 2: Convert leading spaces to tabs in expanded CSS.
console.log('Converting indentation to tabs...');
for (const output of Object.keys(sassFiles)) {
	const filePath = path.join(root, output);
	if (fs.existsSync(filePath)) {
		let css = fs.readFileSync(filePath, 'utf8');
		css = css.replace(/^( +)/gm, (match) => '\t'.repeat(match.length / 2));
		fs.writeFileSync(filePath, css);
	}
}

// Step 3: Replace @@placeholders in style.css.
console.log('Replacing placeholders in style.css...');
const stylePath = path.join(root, 'style.css');
let styleContent = fs.readFileSync(stylePath, 'utf8');
// Sort longest-first to avoid partial matches (e.g. @@author before @@author_url).
const sortedKeys = Object.keys(replacements).sort((a, b) => b.length - a.length);
for (const placeholder of sortedKeys) {
	styleContent = styleContent.split(placeholder).join(replacements[placeholder]);
}
fs.writeFileSync(stylePath, styleContent);

// Step 4: Generate minified CSS.
console.log('Generating minified CSS...');
for (const [output, source] of Object.entries(sassFiles)) {
	const minOutput = output.replace('.css', '.min.css');
	const outputPath = path.join(root, minOutput);
	const sourcePath = path.join(root, source);
	execSync(
		`npx sass --no-error-css --style=compressed --no-source-map "${sourcePath}:${outputPath}"`,
		{ cwd: root, stdio: 'inherit' }
	);
}

// Replace placeholders in style.min.css too.
const minStylePath = path.join(root, 'style.min.css');
if (fs.existsSync(minStylePath)) {
	let minContent = fs.readFileSync(minStylePath, 'utf8');
	for (const placeholder of sortedKeys) {
		minContent = minContent.split(placeholder).join(replacements[placeholder]);
	}
	fs.writeFileSync(minStylePath, minContent);
}

console.log('Build complete.');
