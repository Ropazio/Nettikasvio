<?php

namespace app\Core;

use Aws\S3\S3Client;


class S3 {

    protected S3Client $s3Client;

    public function __construct() {
    
        // Configuration for herbarium images
        $s3Config = [
            "region"            => AWS_REGION,
            "accessKey"         => AWS_ACCESS_KEY,
            "secretKey"         => AWS_SECRET,
            "bucket"            => AWS_BUCKET
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

        return AWS_BUCKET;
    }


    public function getS3Client() : S3Client {

        return $this->s3Client;
    }
}
