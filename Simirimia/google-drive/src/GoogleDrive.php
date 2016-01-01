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
use Google_Http_MediaFileUpload;
use Google_Service_Drive;
use Google_Service_Drive_DriveFile;

class GoogleDrive
{

    /**
     * @var Google_Client
     */
    private $client;
    /**
     * @var Google_Service_Drive
     */
    private $service;

    /**
     * GoogleDrive constructor.
     * @param Google_Client $client
     */
    public function __construct( Google_Client $client )
    {
        $this->client = $client;
        $this->service = new Google_Service_Drive( $this->client );
    }

    /**
     * @param File $inputFile
     * @param string $rootFolderId
     * @return Google_Service_Drive_DriveFile
     * @throws \Exception
     */
    public function add( File $inputFile, string $rootFolderId = '' ) : Google_Service_Drive_DriveFile
    {
        $chunkSize = 1024*1024;

        $file = new Google_Service_Drive_DriveFile();
        $file->title = $inputFile->getTitle();
        $file->description = $inputFile->getDescription();

        if ( $rootFolderId != '' ) {
            $parent = new \Google_Service_Drive_ParentReference();
            $parent->setId( $rootFolderId );
            $file->setParents( [$parent] );
        }

        $request = $this->service->files->insert( $file );
        $media = new Google_Http_MediaFileUpload(
            $this->client,
            $request,
            $inputFile->getMimeType(),
            null,
            true,
            $chunkSize
        );
        $size = filesize($inputFile->getLocalPath().$inputFile->getLocalFileName());
        if ( $size == false ) {
            return $file;
        }

        $media->setFileSize( $size );

        $status = false;
        $handle = fopen( $inputFile->getLocalPath().$inputFile->getLocalFileName(), 'rb' );

        while( !$status && !feof($handle) ) {
            $chunk = $this->readPhotoChunk( $handle, $chunkSize );
            $status = $media->nextChunk( $chunk );
        }

        if ($status == false) {
            throw new \Exception( 'Upload returned false' );
        }

        fclose($handle);
        return $status;
    }

    public function updateMeta( $inputFile, string $rootFolderId = '' ) : Google_Service_Drive_DriveFile
    {
        $file = new Google_Service_Drive_DriveFile();
        $this->service->files->update();
    }

    /**
     * @param $baseFolderId
     * @param $folderName
     * @return Google_Service_Drive_DriveFile
     * @throws \Exception
     */
    public function createFolder( string $baseFolderId, string $folderName ) :Google_Service_Drive_DriveFile
    {

        $file = new Google_Service_Drive_DriveFile();
        $file->title = $folderName;
        $file->setMimeType( 'application/vnd.google-apps.folder' );

        $parent = new \Google_Service_Drive_ParentReference();
        $parent->setId( $baseFolderId );
        $file->setParents( [$parent] );

        $request = $this->service->files->insert( $file );
        $batch = new \Google_Http_Batch( $this->client );
        $batch->add( $request );
        $response = $batch->execute();
        $result = array_pop( $response );


        if ( $result instanceof \Google_Service_Exception ) {
            var_dump( $result );
            throw new \Exception( 'Google Service returned Exception' );
        } else {
            return $result;
        }
    }

    /**
     * @param string $baseFolderId
     * @param string $folderStructure
     * @return string
     * @throws \Exception
     */
    public function createFolderStructure( string $baseFolderId, string $folderStructure ) :string
    {
        $folderParts = explode( '/', $folderStructure );

        foreach( $folderParts as $currentFolderPart ) {

            if ( $currentFolderPart == "" ) {
                continue;
            }

            $sub = $this->getSubFolders( $baseFolderId );

            if ( count($sub->getItems()) == 0 ) {
                printf("Creating folder (1) %s\n", $currentFolderPart);
                $baseFolderId = $this->createFolder( $baseFolderId, $currentFolderPart )->getId();
                continue;
            }

            /** @var Google_Service_Drive_DriveFile $subFolder */
            foreach( $sub as $subFolder ) {

                if ( $subFolder->getTitle() == $currentFolderPart ) {
                    $baseFolderId = $subFolder->getId();
                    continue 2;
                }
            }

            printf("Creating folder (2) %s\n", $currentFolderPart);
            $baseFolderId = $this->createFolder( $baseFolderId, $currentFolderPart )->getId();
        }
        return $baseFolderId;
    }

    /**
     * @param $parentId
     * @return \Google_Service_Drive_FileList
     * @throws \Exception
     */
    public function getSubFolders( $parentId ) :\Google_Service_Drive_FileList
    {
        $optParams = array(
            'q' => 'mimeType = \'application/vnd.google-apps.folder\' and \'' . $parentId . '\' in parents'
        );
        $request = $this->service->files->listFiles($optParams);
        $batch = new \Google_Http_Batch( $this->client );
        $batch->add( $request );
        $response = $batch->execute();
        $result = array_pop( $response );

        if ( $result instanceof \Google_Service_Exception ) {
            var_dump( $result );
            throw new \Exception( 'Google Service returned Exception' );
        } else {
            return $result;
        }

    }

    public function list()
    {
        /*
        $optParams = array(
            //'maxResults' => 3,
            //'q' => 'mimeType = \'application/vnd.google-apps.folder\''
            'q' => 'mimeType = \'application/vnd.google-apps.folder\' and \'0AN_u7fy511WaUk9PVA\' in parents'
            //'q' => 'title contains \'Leasing\''
        );
        */
        $optParams = [];

        $request = $this->service->files->listFiles($optParams);
        $foo = new \Google_Http_Batch( $this->client );
        $foo->add( $request );
        $response = $foo->execute();
        $results = array_pop( $response );

        if ( $results instanceof \Google_Service_Exception ) {

            var_dump( $results );
            throw new \Exception( 'Google Service returned Exception' );

        } else {
            if (count($results->getItems()) == 0) {
                print "No files found.\n";
            } else {
                /** @var Google_Service_Drive_DriveFile $file */
                foreach ($results->getItems() as $file) {
                    printf("%s:: %s %s (%s)\n", get_class($file),
                        $file->getTitle(), $file->getKind(), $file->getId());
                }
            }
        }
    }

    private function readPhotoChunk ( $handle, int $chunkSize )
    {

        if ( !is_resource($handle) ) {
            throw new \Exception( '$handle needs to be a resource' );
        }

        $giantChunk = '';
        $byteCount = 0;
        while ( !feof( $handle )) {

            $chunk = fread( $handle, 8192 );
            $byteCount += strlen( $chunk );
            $giantChunk .= $chunk;

            if ( $byteCount >= $chunkSize ) {
                return $giantChunk;
            }

        }

        return $giantChunk;
    }


}