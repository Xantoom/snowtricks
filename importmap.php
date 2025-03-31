<?php

/**
 * Returns the importmap for this application.
 *
 * - "path" is a path inside the asset mapper system. Use the
 *     "debug:asset-map" command to see the full list of paths.
 *
 * - "entrypoint" (JavaScript only) set to true for any module that will
 *     be used as an "entrypoint" (and passed to the importmap() Twig function).
 *
 * The "importmap:require" command can be used to add new entries to this file.
 */
return [
    'app' => [
        'path' => './assets/app.js',
        'entrypoint' => true,
    ],
	'bootstrap' => [
		'version' => '5.3.3',
	],
	'@popperjs/core' => [
		'version' => '2.11.8',
	],
	'bootstrap/dist/css/bootstrap.min.css' => [
		'version' => '5.3.3',
		'type' => 'css',
	],
	'load-more' => [
		'path' => './assets/js/load-more.js',
		'entrypoint' => true,
	],
	'load-more-comments' => [
		'path' => './assets/js/load-more-comments.js',
		'entrypoint' => true,
	],
	'home' => [
		'path' => './assets/js/pages/home.js',
		'entrypoint' => true,
	],
	'snowtrick-view' => [
		'path' => './assets/js/pages/snowtrick-view.js',
		'entrypoint' => true,
	],
	'snowtrick-edit' => [
		'path' => './assets/js/pages/snowtrick-edit.js',
		'entrypoint' => true,
	],
	'snowtrick-create' => [
		'path' => './assets/js/pages/snowtrick-create.js',
		'entrypoint' => true,
	],
];
