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

class InteractionHandlerCli implements InteractionHandler
{
    public function askForAuthCode( string $authUrl )
    {
        printf("Open the following link in your browser:\n%s\n", $authUrl);
        print 'Enter verification code: ';
        return trim(fgets( STDIN ));
    }
}