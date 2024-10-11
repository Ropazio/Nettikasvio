<?php

namespace app\Models;

use app\{
    Core\S3,
    Models\Model
};
use Aws\S3\{
    S3Client,
    Exception\S3Exception
};


class S3Model extends Model {

    protected S3 $s3;
    protected S3Client $s3Client;

    public function __construct() {
        $this->s3 = new S3();
    }


    public function upload( string $prefix, string $fileName, string $fileTempName) : string {

        $this->s3Client = $this->s3->getS3Client();

        try {
            $result = $this->s3Client->putObject([
                "Bucket"        => $this->s3->getS3Bucket(),
                "Key"           => $prefix . "/" . $fileName,
                "SourceFile"    => $fileTempName
            ]);
            $resultArray = $result->toArray();

            if (!empty($resultArray["ObjectURL"])) {
                return $resultArray["ObjectURL"];
            }
        } catch (S3Exception $e) {
            $error = $e->getMessage();
        }

        if (!empty($error)) {
            echo "Error in uploading files: " . $error;
        }
    }
}