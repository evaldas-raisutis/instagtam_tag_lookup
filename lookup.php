<?php 
require 'source.interface.php';
require 'instagram.source.php';

$sources = array(
	'instagram' => new Instagram(
		'client_id (you can get it from instagram dev page)',
		// array of tags, each tag has a title (to search for) and an id (in case you are using a DB)
		array(
			array('title' => '', 'id' => 0),
			array('title' => '', 'id' => 1)
		)
	)
);

foreach ($sources as $title => $source) {
	render_data($title, $source->ini_lookup());
}

function render_data($source, $data)
{
	print '<pre>';
	print 'Source: ' . $source . ' with ' . count($data) . ' elements.' ;
	print "</br>";
	print_r($data);
	print '/<pre>';
}

 ?>
