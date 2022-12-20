
function formatText(tag) {
	// TODO: make this way better
	document.getElementById('message').value += '['+tag+']'+'[/'+tag+']';
}

// Theme picker for editprofile
if (themepicker = document.getElementById('theme')) {
	themepicker.addEventListener('change', function (e) {
		document.head.getElementsByTagName('link')[1].href = 'themes/'+themepicker.value+'/style.css';
	});
}
