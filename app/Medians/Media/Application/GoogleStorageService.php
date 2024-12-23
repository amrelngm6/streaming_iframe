<?php

namespace Medians\Media\Application;
use Shared\dbaser\CustomController;

use Google\Cloud\Storage\StorageClient;

class GoogleStorageService extends CustomController 
{

	protected $app;

	protected $client;
    
	protected $bucketName;
	
	function __construct()
	{
        // Create a Google Cloud Storage client
        $this->client = new StorageClient([
            'keyFilePath' => $_SERVER['DOCUMENT_ROOT'].'/app/Shared/GoogleStorageService.json' // Path to service account JSON file
        ]);

        // Bucket name
        $this->bucketName = 'medians-streaming';
        
	}

    /**
     * Upload file to Google Storage
     * 
     * @param $filePath String ( Full path ) 
     * @param $destination String  ( Path )
     */
    function uploadFileToGCS($filePath) {

        $destination = str_replace($_SERVER['DOCUMENT_ROOT'], '', $filePath);

        // Get the bucket
        $bucket = $this->client->bucket($this->bucketName);

        // Upload the file
        $file = fopen($filePath, 'r');
        $bucket->upload($file, [
            'name' => $destination // The destination in the bucket
        ]);

        return "$this->bucketName"."$destination";
    }



    function generateSignedUrl( $objectName) {
    
        $bucket = $this->client->bucket($this->bucketName);

        $object = $bucket->object($objectName);
    
        // Generate a signed URL valid for 1 hour (3600 seconds)
        $signedUrl = $object->signedUrl(
            new \DateTime('1 hour'),
            [
                'version' => 'v4',
            ]
        );
    
        return $signedUrl;
    }
    
}