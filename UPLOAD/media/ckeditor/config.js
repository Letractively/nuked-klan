﻿/*
Copyright (c) 2003-2011, CKSource - Frederico Knabben. All rights reserved.
For licensing, see LICENSE.html or http://ckeditor.com/license
*/

CKEDITOR.editorConfig = function( config )
{
	config.resize_enabled = false;
	config.width = '96%';

	// ADVANCED EDITOR || DESACTIVED : SAVE - FORM,CHECKBOX,RADIO,TEXTFILED,TEXTAREA,SELECT,BUTTON,IMAGEBUTTON,HIDDENFIELD - FLASH
	config.toolbar_Full =
	[
		{ name: 'document',    items : [ 'Source','-','NewPage','DocProps','Preview','Print','-','Templates' ] },
		{ name: 'clipboard',   items : [ 'Cut','Copy','Paste','PasteText','PasteFromWord','-','Undo','Redo' ] },
		{ name: 'editing',     items : [ 'Find','Replace','-','SelectAll','-','SpellChecker', 'Scayt' ] },
		{ name: 'links',       items : [ 'Link','Unlink','Anchor' ] },
		'/',
		{ name: 'basicstyles', items : [ 'Bold','Italic','Underline','Strike','Subscript','Superscript','-','RemoveFormat' ] },
		{ name: 'paragraph',   items : [ 'NumberedList','BulletedList','-','Outdent','Indent','-','Blockquote','CreateDiv','-','JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock','-','BidiLtr','BidiRtl' ] },
		{ name: 'tools',       items : [ 'Maximize', 'ShowBlocks','-','About' ] },
		'/',
		{ name: 'styles',      items : [ 'Styles','Format','Font','FontSize' ] },
		{ name: 'colors',      items : [ 'TextColor','BGColor' ] },
		{ name: 'insert',      items : [ 'Youtube','Dailymotion' ] }, // For Youtube, For Dailymotion
		{ name: 'insert',      items : [ 'Image','Table','HorizontalRule','Smiley','SpecialChar' ] },
	];

	// BASIC EDITOR
	config.toolbar_Basic =
	[
		[ 'Source', '-', 'Bold', 'Italic', 'Underline', '-', 'Smiley', 'TextColor', 'RemoveFormat', '-', 'SpellChecker' ]
	];
};
