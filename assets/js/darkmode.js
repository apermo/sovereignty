/**
 * Color-scheme toggle.
 *
 * A collapsed trigger reveals a three-option radiogroup (auto / light / dark)
 * on hover, keyboard focus, or click. The chosen scheme is reflected on the
 * <html> class and persisted to localStorage. The head script applies the
 * stored class before paint; this only handles interaction.
 */
(function () {
	const KEY = 'sovereignty-color-scheme';
	const root = document.documentElement;
	const toggle = document.querySelector('.color-scheme-toggle');

	if (!toggle) {
		return;
	}

	const trigger = toggle.querySelector('.color-scheme-trigger');
	const radios = toggle.querySelectorAll('input[name="color-scheme"]');

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

	function isOpen() {
		return trigger.getAttribute('aria-expanded') === 'true';
	}

	function setOpen(open) {
		trigger.setAttribute('aria-expanded', open ? 'true' : 'false');
	}

	// Sync the control to the scheme the head script already applied.
	const active = current();

	radios.forEach(function (radio) {
		radio.checked = radio.value === active;

		radio.addEventListener('change', function () {
			if (radio.checked) {
				apply(radio.value);
				setOpen(false);
			}
		});
	});

	// Click toggles the panel — the path touch devices rely on (no hover).
	trigger.addEventListener('click', function () {
		setOpen(!isOpen());
	});

	// Close when focus or a click leaves the control, or on Escape.
	document.addEventListener('click', function (event) {
		if (isOpen() && !toggle.contains(event.target)) {
			setOpen(false);
		}
	});

	toggle.addEventListener('focusout', function (event) {
		if (!toggle.contains(event.relatedTarget)) {
			setOpen(false);
		}
	});

	document.addEventListener('keydown', function (event) {
		if (event.key === 'Escape' && isOpen()) {
			setOpen(false);
			trigger.focus();
		}
	});
})();
