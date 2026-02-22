(function () {
	const entryShare = document.getElementById('entry-share');

	if (!entryShare) {
		return false;
	}

	entryShare.onclick = function share() {
		if (navigator.share) {
			navigator.share({
				title: document.querySelector('title').textContent,
				url: document
					.querySelector('link[rel="canonical"]')
					.getAttribute('href'),
			});
		} else {
			const citationOptions = document.getElementById('share-options');
			if (citationOptions.style.display === 'none') {
				citationOptions.style.display = 'block';
			} else {
				citationOptions.style.display = 'none';
			}
		}
		return false;
	};
})();
