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

class CredentialsRepositoryFile implements CredentialsRepository
{
    /**
     * @var string
     */
    private $credentialsPath;

    public function __construct( string $credentialsPath )
    {
        $this->credentialsPath = $this->expandHomeDirectory( $credentialsPath );
    }

    /**
     * @return string
     */
    public function loadAccessToken() :string
    {
        // Load previously authorized credentials from a file.
        if (file_exists( $this->credentialsPath )) {
            return file_get_contents( $this->credentialsPath );
        }
        return '';
    }

    public function storeAccessToken(string $accessToken)
    {
        // Store the credentials to disk.
        if ( !file_exists( dirname( $this->credentialsPath ) )) {
            mkdir( dirname( $this->credentialsPath ), 0700, true );
        }
        printf( "Saving token to: %s\n", $this->credentialsPath );
        file_put_contents( $this->credentialsPath, $accessToken );
    }

    /**
     * Expands the home directory alias '~' to the full path.
     * @param string $path the path to expand.
     * @return string the expanded path.
     */
    private function expandHomeDirectory( string $path ) :string
    {
        $homeDirectory = getenv( 'HOME' );
        if ( empty($homeDirectory) ) {
            $homeDirectory = getenv("HOMEDRIVE") . getenv("HOMEPATH");
        }
        return str_replace('~', realpath( $homeDirectory ), $path);
    }
}