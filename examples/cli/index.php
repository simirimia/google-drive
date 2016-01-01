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
define( 'CREDENTIALS_PATH', __DIR__ . '/../credentials/google-drive-test-application-credentials-cli.json' );
define( 'CLIENT_SECRET_PATH', __DIR__ . '/../config/app_secret_cli.json' );
define( 'SCOPES', Google_Service_Drive::DRIVE );

use Simirimia\GoogleDrive\GoogleDrive;
use Simirimia\GoogleDrive\GoogleClientFactory;
use Simirimia\GoogleDrive\CredentialsRepositoryFile;
use Simirimia\GoogleDrive\InteractionHandlerCli;

$credentialsRepository = new CredentialsRepositoryFile( CREDENTIALS_PATH );
$interactionHandler = new InteractionHandlerCli();

$factory = new GoogleClientFactory( APPLICATION_NAME,
    SCOPES,
    CLIENT_SECRET_PATH,
    $credentialsRepository,
    $interactionHandler );


$googleClient = $factory->create();
$drive = new GoogleDrive( $googleClient );
$drive->list();