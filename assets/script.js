
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
