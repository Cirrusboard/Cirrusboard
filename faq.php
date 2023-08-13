<?php
require('lib/common.php');

//Smilies List
$smiliestext = '';
$x = 0;
foreach ($smilies as $text => $url) {
	if ($x % 6 == 0) $smiliestext .= "<tr>";
	$smiliestext .= sprintf('<td class="b n1"><img class="smiley" src="%s"> %s</td>',
		$url, htmlspecialchars($text));
	$x++;
	if ($x % 6 == 0) $smiliestext .= "</tr>";
}

// Rank colours
$ranktable = '';
foreach ($ranks as $id => $title)
	$ranktable .= sprintf('<td class="n1" width="120"><b><span style="color:#%s">%s</span></b></td>', rankIdToColour($id), $title);

if (file_exists('conf/faq.php'))
	require('conf/faq.php');
elseif(file_exists('conf/faq.sample.php')
	require('conf/faq.sample.php');
else error("404", "No FAQ has been defined."); // this error should also probably appear if the FAQ has nothing in it or has invalid data

echo twigloader()->render('faq.twig', [
	'faq' => $faq
]);
