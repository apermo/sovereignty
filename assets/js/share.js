/**
 * share.js
 *
 * Handles the share button: uses Web Share API if available,
 * otherwise toggles a fallback share options panel.
 */
(function () {
	const entryShare = document.getElementById('entry-share');

	if (!entryShare) {
		return;
	}

	entryShare.addEventListener('click', function (event) {
		event.preventDefault();

		if (navigator.share) {
			navigator.share({
				title: document.title,
				url:
					document
						.querySelector('link[rel="canonical"]')
						?.getAttribute('href') || window.location.href,
			});
		} else {
			const shareOptions = document.getElementById('share-options');
			if (shareOptions) {
				shareOptions.classList.toggle('is-visible');
			}
		}
	});
})();
