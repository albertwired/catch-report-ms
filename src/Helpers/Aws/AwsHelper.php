<?php
declare(strict_types=1);
 
namespace App\Helpers\Aws;
 
use Aws\Sdk;
 
abstract class AwsHelper
{
    protected $sdk;
 
    public function __construct(Sdk $sdk)
    {
        $this->sdk = $sdk;
    }
}