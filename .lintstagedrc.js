const shellEscape = (s) => `'${s.replace(/'/g, "'\\''")}'`;

module.exports = {
	'*.js': (files) => [`eslint ${files.map(shellEscape).join(' ')}`],
	'*.scss': (files) => [`stylelint ${files.map(shellEscape).join(' ')}`],
	'*.php': (files) => [
		`vendor/bin/phpcs --warning-severity=0 ${files.map(shellEscape).join(' ')}`,
		'vendor/bin/phpstan analyse --no-progress --memory-limit=512M',
	],
};
