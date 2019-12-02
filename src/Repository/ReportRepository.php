<?php
declare(strict_types=1);

namespace App\Repository;

use App\Entity\Report;
use App\Helpers\Aws\AwsS3Helper;
use Cerbero\JsonObjects\JsonObjects;

class ReportRepository {

    protected $s3;
    public function __construct(AwsS3Helper $s3)
    {
        $this->s3 = $s3;
        $this->store = [];
    }

    public function streamS3(Type $var = null)
    {
        $this->store = [];
        $source = $this->s3->getObject('catch-anggi', 'challenge-1-in.jsonl');
        JsonObjects::from($source)->each(function (array $object) {
            $this->storeData($object);
        });
        return $this->store;
    }
    
    public function storeData($data)
    {
        $result = [
            'order_id'=>$data['order_id'],
            'order_datetime'=>date('Y-m-d H:i:s', strtotime($data['order_date'])),
            'item_prices'=>[],
            'total_order_value' => 0,
            'distinct_unit_count'=> count($data['items']),
            'total_units_count'=> 0,
            'customer_state'=>$data['customer']['shipping_address']['state']
        ];
        foreach($data['items'] as $items){
          $result['total_order_value'] += ($items['quantity'] * $items['unit_price']);
          $result['total_units_count'] += $items['quantity'];
          $result['item_prices'][] = $items['unit_price'];
        }
        $result['average_item_price'] = array_sum($result['item_prices'])/count($result['item_prices']);
        unset($result['item_prices']);
        $this->store[]=$result;
    }
}