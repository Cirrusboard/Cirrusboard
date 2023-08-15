<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1">
	<title><?=$config['title']?></title>
	<link rel="stylesheet" href="/assets/css/style.css">
	<link rel="stylesheet" href="/themes/<?=$config['defaulttheme']?>/style.css">
</head>
<body>
	<img class="boardlogo" src="<?=$config['logo']?>" style="max-width:100%;margin:20px auto;display:block;">

	<table class="c1 faq" style="max-width:640px;margin:auto">
		<tr><td class="n1">
			<?=$config['maintenance_msg']?>
		</td></tr>
	</table>
</body>
</html>
