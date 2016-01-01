<?php
/*
* This file is part of the simirimia/google-drive package.
*
* (c) https://github.com/simirimia
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

declare( strict_types = 1 );
require __DIR__ . '/../../vendor/autoload.php';

define( 'APPLICATION_NAME', 'Google Drive Test Application' );
define( 'CREDENTIALS_PATH', __DIR__ . '/../credentials/google-drive-test-application-credentials-http.json' );
define( 'CLIENT_SECRET_PATH', __DIR__ . '/../config/app_secret_http.json' );
define( 'SCOPES', Google_Service_Drive::DRIVE );
define( 'DRIVE_AUTH_SUCCESS_REDIRECT_URL', 'http://sgd.app/redirecttarget.php' );

use Simirimia\GoogleDrive\CredentialsRepositoryFile;
use Simirimia\GoogleDrive\InteractionHandlerHttp;
use Simirimia\GoogleDrive\GoogleClientFactory;

$credentialsRepository = new CredentialsRepositoryFile( CREDENTIALS_PATH );
$interactionHandler = new InteractionHandlerHttp();

$factory = new GoogleClientFactory( APPLICATION_NAME,
    SCOPES,
    CLIENT_SECRET_PATH,
    $credentialsRepository,
    $interactionHandler );


if ( isset( $_GET['code'] ) ) {
    $factory->create( $_GET['code'] );
    header( 'location:' . DRIVE_AUTH_SUCCESS_REDIRECT_URL );
} else {
    die( 'something went wrong....' );
}
