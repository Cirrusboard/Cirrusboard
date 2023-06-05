<h1>Cirrusboard Installer by compa-c</h1>
<hr>
<?php

	$contactyourwebhost = "<br>Contact your web hosting provider if you require further assistance.";
	
	if (version_compare(phpversion(), '7.3.0', '<'))
		die("Your current version of PHP is ".phpversion().". Please upgrade to PHP 8.2 or newer in order to continue installing.".$contactyourwebhost."");

	if (!file_exists("composer.lock"))
		die("The could not be located.".$contactyourwebhost."");

	if (!extension_loaded('pdo_mysql'))
		die("The PDO MySQL library is not installed on your web server. Please install the library in order to continue installing.".$contactyourwebhost."");

	// I had to do this
	if (420 - 69 == 360)
		die("The server is warped! Warped, I say! Therefore, I, the installer script, have had to prevent you from installing Cirrusboard to ensure your safety within this illogical server environment that could declare you as a sausage roll in a random constant at any given moment.");
	
	if (!file_exists("userpic/"))
		mkdir("userpic"); // TODO: make it return fail if it can't write.
	
	if (isset($_POST['action']) && $_POST['action'] == "Install")
	{
		// write the config file
		$sysfile = "<?php
\$host = '".$_POST['sqlhost']."';
\$db   = '".$_POST['sqldb']."';
\$user = '".$_POST['sqluser']."';
\$pass = '".$_POST['sqlpass']."';

\$tplCache = 'templates/cache';
//\$tplCache = '/tmp/cirrusboard';
\$tplNoCache = false; // **DO NOT SET AS TRUE IN PROD - DEV ONLY**

// Customise your forum
\$config['title'] = '".$_POST['forumname']."';
\$config['description'] = 'The owner of this Cirrusboard website was too lazy to edit the description in config.php. Kill him.';
\$config['logo'] = '".$_POST['logourl']."';
//\$config['logotitle'] = ''; // Add a title attribute to the logo, which will be visible when hovered on it.
//\$config['tagline'] = ''; // Add a tagline which will be shown in the header right below the logo.

\$config['newsid'] = -1; // Designates the id for your announcements forum, the latest post will be shown at the top of the index page.

\$config['defaulttheme'] = 'voxelmanip_retro';
\$config['defaulttimezone'] = '".date_default_timezone_get()."'; // Default timezone if people do not select their own.

// Allow HTML in posts? A reasonable attempt is done to scrub JavaScript and other dangerous HTML elements, but tread with caution.
\$config['html'] = true;

// This will enable Acmlmboard-style post layouts where users can customise their post layout with CSS. Requires HTML be enabled.
// When disabled, it will turn the signature into a regular forum one. Please be careful with disabling this on an existing forum
// as users with post layouts will be cut in half and create trailing HTML elements that break the layout.
\$config['postlayouts'] = true;

// Uncomment to replace the footer with your own thing. We won't mind if you remove the credits from the footer!
// (As long as it remains in the /credits.php page and the LICENSE file is kept)
//\$config['customfooter'] = <<<HTML
// (put some stuff inside here)
//HTML;

// List of smilies, if you want them. Check the Cirrusboard Wiki for smiley packs.
\$smilies = [
	//':)' => 'assets/smilies/smile.png',
];

// Sample post that is shown on profile pages.
\$samplepost = <<<HTML
This is a sample post. It [b]tests[/b] [i]your[/i] [u]post layout[/s] against a variety of forum markup.
[quote=Anonymous][quote=some guy]Hi. This is a test quote, with a [url=http://www.google.com/]link[/url].[/quote]And this is a reply. How cool is that? :)[/quote]
[code]if (true) {\r
	print \"The world isn't broken.\";\r
} else {\r
	print \"Something is very wrong.\";\r
}[/code]
[url=]Test Link. Ooh![/url]
[spoiler]No way, I can't believe this! It's a spoiler![/spoiler]
HTML;
?>
";
			
			file_put_contents('conf/config.php', $sysfile); // todo: return error if this fails.
			
			// we need to pre-define these so we can do the next bit
			
			$host = $_POST['sqlhost'];
			$db = $_POST['sqldb'];
			$user = $_POST['sqluser'];
			$pass = $_POST['sqlpass'];
			
			include('lib/mysql.php');
			
			// get sql/forum.sql and then import it.

			$queries = file_get_contents('sql/forum.sql');
			$queries = explode(';', $queries);
			foreach ($queries as $query)
				query($query);
				
			// if something screws up, this SHOULD be ideally handled already by lib/mysql.php and terminate the rest of the script, so no worries about that.
			
			// else, we have "GREAT SUCCESS" !!
			
			die("Your new board has been successfully installed. Click <a href='index.php'>here</a> to access it.");
	}
	
	elseif (file_exists("conf/config.php"))
			die("The board is already installed. You don't need to run the installer!");
		
	else
	{
		print "
	<form action='installer.php' method='post'>
			<table>
				<tr>
					<td>
						<label for='sqlhost'>MySQL server</label>
					</td>
					<td>
						<input type='text' id='sqlhost' name='sqlhost' value='localhost:3306' required />
					</td>
				</tr>
				<tr>
					<td>
						<label for='sqluser'>Username</label>
					</td>
					<td>
						<input type='text' id='sqluser' name='sqluser' required />
					</td>
				</tr>
				<tr>
					<td>
						<label for='sqlpass'>Password</label>
					</td>
					<td>
						<input type='password' id='sqlpass' name='sqlpass' required />
					</td>
				</tr>
				<tr>
					<td>
						<label for='sqldb'>Database name</label>
					</td>
					<td>
						<input type='text' id='sqldb' name='sqldb' value='cirrus' required />
					</td>
				</tr>
				<tr>
					<td>
						<label for='forumname'>Forum name</label>
					</td>
					<td>
						<input type='text' id='forumname' name='forumname' value='Cool Forum' required />
					</td>
				</tr>
				<tr>
					<td>
						<label for='logourl'>Logo URL</label>
					</td>
					<td>
						<input type='text' id='logourl' name='logourl' value='assets/logo_placeholder.png' required />
					</td>
				</tr>
				<tr>
					<td>
						<input type='submit' id='action' name='action' value='Install' />
					</td>
				</tr>
		</table>
	</form>
	<hr>
	<h2>Notes</h2>
	<ul>
	<li>You can further audit the configuration in conf/config.php after installing. (If on MacOS or iOS, please disable function labelled like 'smart quotes' in your keyboard settings or else you risk breaking the file.)</li>
	<li>If you need software documentation, support, downloadable resources or more, check out the official <a href='http://cirrus.voxelmanip.se/'>Cirrusboard</a> website.</li>
	<li>Upgrading from previous builds using this script is currently not supported.</li>
	<li>Enjoy using your board!</li>
	</ul>
		";
	}

// This makes YandereDev look like a good coder. Either way, this is only a quick and dirty hack more than anything else. --compa-c
?>