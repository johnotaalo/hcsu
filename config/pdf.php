<?php

return [
	'mode'                  => 'utf-8',
	'format'                => 'A4',
	'author'                => '',
	'subject'               => '',
	'keywords'              => '',
	'creator'               => 'Host Country Services Unit',
	'display_mode'          => 'fullpage',
	'tempDir'               => base_path('../temp/'),
	'font_path'				=> public_path('fonts/'),
	'font_data'				=> [
		'lato' => [
			'R'  => 'Lato/Lato-Regular.ttf',    // regular font
			'B'  => 'Lato/Lato-Black.ttf',       // optional: bold font
			'I'  => 'Lato/Lato-Italic.ttf',     // optional: italic font
			'BI' => 'Lato/Lato-BlackItalic.ttf' // optional: bold-italic font
		]
	]
];
