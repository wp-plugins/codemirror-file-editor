<?php
/*
Plugin Name: CodeMirror File Editor
Description: Replace default theme and plugin editor with CodeMirror
Author: Viacheslav Zavoruev
Version: 1.1.1
License: GPLv2 or later
*/

function cmfe_admin_enqueue_scripts( $hook ) {
	if ( $hook != 'theme-editor.php' && $hook != 'plugin-editor.php')
		return;

	global $file;

	$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
	$mode = null;

	wp_register_style( 'codemirror', plugins_url( 'codemirror/lib/codemirror.css', __FILE__ ), null, '5.4.0' );
	wp_register_style( 'cmfe', plugins_url( 'plugin.css', __FILE__ ), array( 'codemirror' ), '1.1.1' );

	wp_enqueue_style( 'cmfe' );

	wp_register_script( 'requirejs', plugins_url( "require$suffix.js", __FILE__ ), null, '2.1.18' );
	wp_register_script( 'cmfe', plugins_url( 'plugin.js', __FILE__ ), array( 'requirejs' ), '1.1.1' );

	$scripts = array(
		'lib/codemirror',
		'addon/display/fullscreen',
		'keymap/sublime',
	);

	if ( !empty( $file )) {
		$ext = substr( $file, strrpos( $file, '.' ) + 1 );

		switch ( $ext ) {
			case 'css':
				$mode = $ext;

				array_push( $scripts,
					'addon/edit/closebrackets',
					'addon/edit/matchbrackets',
					'addon/hint/show-hint',
					'addon/hint/css-hint'
				);
				break;

			case 'html':
				$mode = 'htmlmixed';

				array_push( $scripts,
					'addon/edit/closebrackets',
					'addon/edit/closetag',
					'addon/edit/matchbrackets',
					'addon/edit/matchtags',
					'addon/hint/show-hint',
					'addon/hint/css-hint',
					'addon/hint/html-hint',
					'addon/hint/javascript-hint'
				);
				break;

			case 'js':
				$mode = 'javascript';

				array_push( $scripts,
					'addon/edit/closetag',
					'addon/edit/matchtags',
					'addon/hint/show-hint',
					'addon/hint/javascript-hint'
				);
				break;

			case 'php':
				$mode = $ext;

				array_push( $scripts,
					'addon/edit/closebrackets',
					'addon/edit/closetag',
					'addon/edit/matchbrackets',
					'addon/edit/matchtags'
				);
				break;
		}
	}

	if ( !empty( $mode ) ) {
		$scripts[] = 'mode/' . $mode . '/' . $mode;
	}

	wp_localize_script( 'cmfe', 'cmfe', array(
		'requireJsConfig' => array(
			'baseUrl' => plugins_url( 'codemirror', __FILE__ ),
		),
		'codeMirrorConfig' => array (
			'autoCloseBrackets' => true,
			'autoCloseTags' => true,
			'indentUnit' => 8,
			'indentWithTabs' => true,
			'lineNumbers' => true,
			'keyMap' => 'sublime',
			'matchBrackets' => true,
			'matchTags' => true,
			'mode' => $mode,
			'tabSize' => 8,
		),
		'scripts' => $scripts,
	), '1.0.0');

	wp_enqueue_script( 'cmfe' );
}
add_action( 'admin_enqueue_scripts', 'cmfe_admin_enqueue_scripts' );