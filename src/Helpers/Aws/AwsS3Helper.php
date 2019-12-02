<?php
declare(strict_types=1);
 
namespace App\Helpers\Aws;
 
use App\Exception\AwsUtilException;
use Aws\S3\Exception\S3Exception;
use GuzzleHttp\Psr7\Stream;
 
class AwsS3Helper extends AwsHelper
{
    public function getObject(string $bucket, string $key): Stream
    {
        try {
            $result = $this->sdk->createS3()->getObject(['Bucket' => $bucket, 'Key' => $key]);
 
            return $result['Body'];
        } catch (S3Exception $e) {
            echo $e->getMessage() . PHP_EOL;
        }
    }
}