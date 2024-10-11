<?php

namespace app\Core;

use Aws\S3\S3Client;


class S3 {

    protected S3Client $s3Client;

    public function __construct() {
    
        // Configuration for herbarium images
        $s3Config = [
            "region"            => "eu-north-1",
            "accessKey"         => "",
            "secretKey"         => "",
            "bucket"            => "herbarium-images"
        ];

        $this->s3Client = new S3Client([
            "region"            => $s3Config["region"],
            "credentials"       => [
                "key"       =>  $s3Config["accessKey"],
                "secret"    =>  $s3Config["secretKey"]
            ]
        ]);
    }


    public function getS3Bucket() : string {

        return "herbarium-images";
    }


    public function getS3Client() : S3Client {

        return $this->s3Client;
    }
}
