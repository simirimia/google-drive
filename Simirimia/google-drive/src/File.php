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


interface File
{
    public function getTitle() :string;
    public function getDescription() :string;
    public function getMimeType() :string;
    public function getLocalFileName() :string;
    public function getLocalPath() :string;

    public function getGoogleId() :string;
    public function setGoogleId( string $id );

    public function getGoogleUrl() :string;
    public function setGoogleUrl( string $url );

    public function getCurrentRevision() :string;
    public function setCurrentRevision( string $revision );
}