/**
 * Color-scheme toggle.
 *
 * Syncs a three-option radiogroup (auto / light / dark) with the class on
 * <html> and persists the choice to localStorage. The head script applies the
 * stored class before paint; this only handles user interaction.
 */
(function () {
	const KEY = 'sovereignty-color-scheme';
	const root = document.documentElement;
	const radios = document.querySelectorAll('input[name="color-scheme"]');

	if (!radios.length) {
		return;
	}

	function current() {
		if (root.classList.contains('dark-mode')) {
			return 'dark';
		}

		if (root.classList.contains('light-mode')) {
			return 'light';
		}

		return 'auto';
	}

	function apply(value) {
		root.classList.remove('auto-mode', 'dark-mode', 'light-mode');
		root.classList.add(value + '-mode');

		try {
			localStorage.setItem(KEY, value);
		} catch (e) {}
	}

	const active = current();

	radios.forEach(function (radio) {
		radio.checked = radio.value === active;

		radio.addEventListener('change', function () {
			if (radio.checked) {
				apply(radio.value);
			}
		});
	});
})();
