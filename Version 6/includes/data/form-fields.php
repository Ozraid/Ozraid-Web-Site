<?php
namespace Ozraid\Includes\Data;

$fields = new \stdClass();

// <input type="text"> tags.
$fields->name = array (
	'tag'         => 'input',
	'type'        => 'text',
	'name'        => 'name',
	'maxlength'   => 64,
	'placeholder' => 'Your name...',
	'required'    => 'required',
	'data-type'   => 'name'
);
$fields->login = array (
	'tag'         => 'input',
	'type'        => 'text',
	'name'        => 'login',
	'maxlength'   => 64,
	'placeholder' => 'Your login name...',
	'required'    => 'required',
	'data-type'   => 'string'
);
$fields->username = array (
	'tag'         => 'input',
	'type'        => 'text',
	'name'        => 'username',
	'maxlength'   => 24,
	'placeholder' => 'Your username...',
	'required'    => 'required',
	'data-type'   => 'name'
);
$fields->character = array (
	'tag'         => 'input',
	'type'        => 'text',
	'name'        => 'character[]',
	'maxlength'   => 24,
	'placeholder' => 'Your character\'s name...',
	'required'    => 'required',
	'data-type'   => 'character'
);
$fields->twitter_handle = array (
	'tag'         => 'input',
	'type'        => 'text',
	'name'        => 'twitter-handle',
	'maxlength'   => 16,
	'placeholder' => 'Your Twitter handle...',
	'data-type'   => 'twitter'
);
$fields->referee_name = array (
	'tag'         => 'input',
	'type'        => 'text',
	'name'        => 'referee-name',
	'maxlength'   => 64,
	'placeholder' => 'Referred by...',
	'data-type'   => 'name'
);

// <input type="number"> tags.
$fields->mobile = array (
	'tag'         => 'input',
	'type'        => 'number',
	'name'        => 'mobile',
	'maxlength'   => 24,
	'placeholder' => 'Your cell or mobile number...',
	'data-type'   => 'integer'
);

// <input type="password"> tags.
$fields->password = array (
	'tag'         => 'input',
	'type'        => 'password',
	'name'        => 'password',
	'maxlength'   => 256,
	'placeholder' => 'Your password...',
	'required'    => 'required',
	'data-type'   => 'string'
);
$fields->confirm_password = array (
	'tag'         => 'input',
	'type'        => 'password',
	'name'        => 'confirm-password',
	'maxlength'   => 256,
	'placeholder' => 'Confirm your password...',
	'required'    => 'required',
	'data-type'   => 'string'
);

// <input type="email"> tags.
$fields->email = array (
	'tag'         => 'input',
	'type'        => 'email',
	'name'        => 'email',
	'maxlength'   => 256,
	'placeholder' => 'Your email address...',
	'required'    => 'required'
);
$fields->confirm_email = array (
	'tag'         => 'input',
	'type'        => 'email',
	'name'        => 'confirm-email',
	'maxlength'   => 256,
	'placeholder' => 'Confirm your email address...',
	'required'    => 'required'
);

// <input type="url"> tags.
$fields->facebook = array (
	'tag'         => 'input',
	'type'        => 'url',
	'name'        => 'facebook',
	'maxlength'   => 256,
	'placeholder' => 'Your Facebook page...',
	'data-type'   => 'url'
);

// <input type="checkbox"> tags.
$fields->mic = array (
	'tag'         => 'input',
	'type'        => 'checkbox',
	'id'          => 'mic',
	'name'        => 'mic',
	'required'    => 'required',
	'data-type'   => 'boolean'
);
$fields->raid_ready = array (
	'tag'         => 'input',
	'type'        => 'checkbox',
	'id'          => 'raid-ready',
	'name'        => 'raid-ready[]',
	'required'    => 'required',
	'data-type'   => 'boolean'
);
$fields->policies = array (
	'tag'         => 'input',
	'type'        => 'checkbox',
	'id'          => 'policies',
	'name'        => 'policies',
	'required'    => 'required',
	'data-type'   => 'boolean'
);

// <select> tags.
$fields->subject = array (
	'tag'         => 'select',
	'name'        => 'subject',
	'placeholder' => 'Select a subject...',
	'required'    => 'required',
	'data-type'   => 'integer'
);
$fields->country = array (
	'tag'         => 'select',
	'name'        => 'country',
	'placeholder' => 'Select your country...',
	'required'    => 'required',
	'data-type'   => 'integer'
);
$fields->timezone = array (
	'tag'         => 'select',
	'name'        => 'timezone',
	'placeholder' => 'Select your timezone...',
	'required'    => 'required',
	'data-type'   => 'integer'
);

// <textarea> tags.
$fields->message = array (
	'tag'         => 'textarea',
	'name'        => 'message',
	'placeholder' => 'Enter your message here...',
	'required'    => 'required',
	'data-type'   => 'text'
);
$fields->reason = array (
	'tag'         => 'textarea',
	'name'        => 'reason',
	'placeholder' => 'Enter your reason here...',
	'required'    => 'required',
	'data-type'   => 'text'

);
$fields->background = array (
	'tag'         => 'textarea',
	'name'        => 'message',
	'placeholder' => 'Enter a few brief notes about your background here...',
	'required'    => 'required',
	'data-type'   => 'text'
);


// Write JSON file.
$pathname = $_SERVER['DOCUMENT_ROOT'] .'/includes/json/form-fields.json';
if ( file_exists ( $pathname ) ) {
	unlink ( $pathname );
}
$file = fopen ( $pathname, 'w' );
fwrite ( $file, json_encode ( $fields, JSON_FORCE_OBJECT ) );
fclose ( $file );
if ( file_exists ( $pathname ) ) {
	echo 'Form fields file "/json/form-fields.json" successfully saved.';
}
else {
	echo 'Form fields PHP error.';
}