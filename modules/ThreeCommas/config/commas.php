<?php

/**
 * Base configurations for 3Commas
 * 
 * @link https://github.com/3commas-io/3commas-official-api-docs
 */
return [
    /*
	|------------------------------------------------------------------------------
	| Base API URI
	|------------------------------------------------------------------------------
	|
	| A basic uri of api
	*/
    'base_uri' => env('3COMMAS_BASE_ENDPOINT', 'https://api.3commas.io/public/api'),
	
    /*
	|------------------------------------------------------------------------------
	| 3Commas API Key and secret
	|------------------------------------------------------------------------------
	|
	| Login and create a key/secret pair in https://3commas.io/api_access_tokens
	*/
	'api_key' => env('3COMMAS_API_KEY', ''),
	'secret_key' => env('3COMMAS_SECRET_KEY', '')
];