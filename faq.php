<?php
require('lib/common.php');

// Powerlevel colours
$nctable = '';
foreach ($powerlevels as $id => $title)
	$nctable .= sprintf('<td class="n1" width="140"><b><span style="color:#%s">%s</span></b></td>', powIdToColour($id), $title);

if (file_exists('conf/faq.php'))
	require('conf/faq.php');
else
	require('conf/faq.sample.php');

echo twigloader()->render('faq.twig', [
	'faq' => $faq
]);
