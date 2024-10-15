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


    public function upload( string $prefix, string $fileName, string $tempFilePath) : string {

        $this->s3Client = $this->s3->getS3Client();

        try {
            $result = $this->s3Client->putObject([
                "Bucket"        => $this->s3->getS3Bucket(),
                "Key"           => $prefix . "/" . $fileName,
                "SourceFile"    => $tempFilePath . "/" . $fileName
            ]);
            $resultArray = $result->toArray();

            if (!empty($resultArray["ObjectURL"])) {
                return $resultArray["ObjectURL"];
            }
        } catch (S3Exception $e) {
            $error = $e->getMessage();
        }

        if (!empty($error)) {
            echo "Error occurred while uploading files: " . $error;
        }
    }


    public function deleteImagesFromBucket( array $images ) {

        $this->s3Client = $this->s3->getS3Client();
        $bucket = $this->s3->getS3Bucket();

        foreach ($images as $image) {
            if (!$image) {
                continue;
            } else {
                list($fileName, $prefix) = array_reverse(explode("/", $image));
                try {
                    $this->s3Client->deleteObject([
                        "Bucket"        => $bucket,
                        "Key"           => $prefix . "/" . $fileName
                    ]);
                    $this->s3Client->deleteObject([
                        "Bucket"        => $bucket,
                        "Key"           => "_thumbnails/" . $prefix . "/" . $fileName
                    ]);

                } catch (S3Exception $e) {
                    $error = $e->getMessage();
                }

                if (!empty($error)) {
                    echo "Error occurred while deleting files: " . $error;
                }
            }
        }
    }
}