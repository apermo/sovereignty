/**
 * share.js
 *
 * Wires up the share button: uses the Web Share API when the browser can
 * handle the post, and otherwise — or if sharing fails — reveals the fallback
 * panel with the post's shortlink, permalink and citation HTML.
 */
(function () {
	const entryShare = document.getElementById('entry-share');

	if (!entryShare) {
		return;
	}

	const toggleFallback = function () {
		const shareOptions = document.getElementById('share-options');
		if (shareOptions) {
			shareOptions.classList.toggle('is-visible');
		}
	};

	entryShare.addEventListener('click', async function (event) {
		event.preventDefault();

		const shareData = {
			title: document.title,
			url:
				document
					.querySelector('link[rel="canonical"]')
					?.getAttribute('href') || window.location.href,
		};

		// Prefer the native share sheet wherever it exists; fall back to the
		// panel only when Web Share is absent or the share actually fails
		// (but not when the user simply cancels the sheet).
		if (navigator.share) {
			try {
				await navigator.share(shareData);
				return;
			} catch (error) {
				if (error.name === 'AbortError') {
					return;
				}
			}
		}

		toggleFallback();
	});
})();
