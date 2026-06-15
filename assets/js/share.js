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

		// Web Share is reliable on mobile but flaky/absent on desktop, so fall
		// back to the panel whenever it cannot handle the post or the share is
		// dismissed for any reason other than the user cancelling it.
		if (navigator.canShare && navigator.canShare(shareData)) {
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
