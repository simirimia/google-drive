<?php
/*
* This file is part of the simirimia/google-drive package.
*
* (c) https://github.com/simirimia
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

declare(strict_types=1);
namespace Simirimia\GoogleDrive;

use Google_Client;

class GoogleClientFactory
{
    /**
     * @var string
     */
    private $applicationName;
    /**
     * @var string
     */
    private $scopes;
    /**
     * @var string
     */
    private $clientSecretPath;
    /**
     * @var CredentialsRepository
     */
    private $credentialsRepository;
    /**
     * @var InteractionHandler
     */
    private $interactionHandler;


    public function __construct( string $applicationName,
                                 string $scopes,
                                 string $clientSecretPath,
                                 CredentialsRepository $credentialsRepository,
                                 InteractionHandler $interactionHandler )
    {
        $this->applicationName = $applicationName;
        $this->scopes = $scopes;
        $this->clientSecretPath = $clientSecretPath;
        $this->interactionHandler = $interactionHandler;
        $this->credentialsRepository = $credentialsRepository;
    }

    /**
     * @return Google_Client
     * @throws PermissionException
     * @throws \Exception
     */
    public function create( string $authCode = '' ) :Google_Client
    {
        $client = new Google_Client();
        $client->setApplicationName( $this->applicationName );
        $client->setScopes( $this->scopes );
        $client->setAuthConfigFile( $this->clientSecretPath );
        $client->setAccessType( 'offline' );

        $accessToken = $this->credentialsRepository->loadAccessToken();

        if( empty($accessToken) ) {

            if ( empty( $authCode ) ) {
                $authUrl = $client->createAuthUrl();
                $authCode = $this->interactionHandler->askForAuthCode( $authUrl );
            }
            $accessToken = $client->authenticate( $authCode );
            $this->credentialsRepository->storeAccessToken( $accessToken );

        }
        $client->setAccessToken( $accessToken );

        // Refresh the token if it's expired.
        if ( $client->isAccessTokenExpired() ) {
            $client->refreshToken( $client->getRefreshToken() );
            $this->credentialsRepository->storeAccessToken( $client->getAccessToken() );
        }

        $client->setDefer( true );

        if ( ! $client->getAccessToken() ) {
            throw new PermissionException( 'No access token' );
        }

        return $client;
    }

}