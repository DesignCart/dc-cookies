document.addEventListener('DOMContentLoaded', function () {
	const bar = document.getElementById('dc-cookie-consent');
	if (!bar) return;

	bar.querySelector('.accept').addEventListener('click', function () {
		document.cookie = "dc_cookie_accepted=1; path=/; max-age=31536000"; // 1 year
		bar.remove();
	});
});