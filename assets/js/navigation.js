/**
 * navigation.js
 *
 * Handles toggling the navigation menu for small screens and enables tab
 * support for dropdown menus.
 */
(function () {
	const container = document.getElementById('site-navigation');

	if (!container) {
		return;
	}

	const button = container.querySelector('button');
	if (!button) {
		return;
	}

	const menu = container.querySelector('ul');

	// Hide menu toggle button if menu is empty and return early.
	if (!menu) {
		button.hidden = true;
		return;
	}

	menu.setAttribute('aria-expanded', 'false');
	menu.classList.add('nav-menu');

	button.addEventListener('click', function () {
		const isToggled = container.classList.toggle('toggled');
		button.setAttribute('aria-expanded', String(isToggled));
		menu.setAttribute('aria-expanded', String(isToggled));
	});

	// Set menu items with submenus to aria-haspopup="true".
	for (const subMenu of menu.querySelectorAll('ul')) {
		subMenu.parentNode.setAttribute('aria-haspopup', 'true');
	}

	// Each time a menu link is focused or blurred, toggle focus.
	for (const link of menu.querySelectorAll('a')) {
		link.addEventListener('focus', toggleFocus, true);
		link.addEventListener('blur', toggleFocus, true);
	}

	/**
	 * Sets or removes .focus class on ancestor li elements.
	 */
	function toggleFocus() {
		let self = this;

		// Move up through the ancestors of the current link until we hit .nav-menu.
		while (!self.classList.contains('nav-menu')) {
			if (self.tagName.toLowerCase() === 'li') {
				self.classList.toggle('focus');
			}
			self = self.parentElement;
		}
	}
})();
