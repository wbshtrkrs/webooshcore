<?php

function getPrefixTitleDetails(){
	$map = [
		'en' => 'Create',
		'id' => 'Buat'
	];
	if (!empty(env('CMS_LANGUAGE'))) $language = env('CMS_LANGUAGE');
	else $language = 'en';
	return $map[$language];

}

function getCancelLabel(){
    $map = [
        'en' => 'Cancel',
        'id' => 'Batal'
    ];
    if (!empty(env('CMS_LANGUAGE'))) $language = env('CMS_LANGUAGE');
    else $language = 'en';
    return $map[$language];

}

function getSubmitLabel(){
    $map = [
        'en' => 'Submit',
        'id' => 'Simpan'
    ];
    if (!empty(env('CMS_LANGUAGE'))) $language = env('CMS_LANGUAGE');
    else $language = 'en';
    return $map[$language];

}

