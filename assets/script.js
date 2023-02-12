
function formatText(tag) {
	// TODO: make this way better
	document.getElementById('message').value += '['+tag+']'+'[/'+tag+']';
}

var buttons = [
	'b', 'i', 'u', 's',
	'url', 'spoiler', 'quote', 'code', 'pre',
	'img', 'youtube'];

if (document.getElementById('postformatting')) {
	for (var i = 0; i < buttons.length; i++) {
		document.getElementById('postformatting-'+buttons[i])
		.addEventListener('click', function (e) {
			formatText(e.target.id.replace('postformatting-', ''));
		});
	}
}

// Theme picker for editprofile
if (themepicker = document.getElementById('theme')) {
	themepicker.addEventListener('change', function (e) {
		document.head.getElementsByTagName('link')[1].href = 'themes/'+themepicker.value+'/style.css';
	});
}

// Manageforums stuff...

if (delforum = document.getElementById('delforum')) {
	delforum.addEventListener('click', function (e) {
		if (!confirm('Really delete this forum?'))
			e.preventDefault();
	});
}

// meow
if (delcat = document.getElementById('delcat')) {
	delcat.addEventListener('click', function (e) {
		if (!confirm('Really delete this category?'))
			e.preventDefault();
	});
}

if (mfback = document.getElementById('mfback')) {
	mfback.addEventListener('click', function (e) {
		window.location = 'manageforums.php';
	});
}
