<?php
declare(strict_types=1);

namespace App\Repository;

use App\Entity\Report;
use App\Helpers\Aws\AwsS3Helper;
use Cerbero\JsonObjects\JsonObjects;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class ReportRepository extends ServiceEntityRepository{

    protected $s3;
    public function __construct(AwsS3Helper $s3,ManagerRegistry $registry,MailerInterface $mailer)
    {
        parent::__construct($registry, Report::class);
        $this->s3 = $s3;
        $this->store = [];
        $this->bucket = $_ENV['AWS_BUCKET_NAME'];
        $this->source = $_ENV['REPORT_FILE'];
        $this->mailer = $mailer;
    }

    public function streamS3()
    {
        $this->store = [];
        $source = $this->s3->getObject($this->bucket, $this->source);
        JsonObjects::from($source)->chunk((int) $_ENV['REPORT_CHECK_LIMIT'],function (array $object) {
            foreach ($object as $obj) {
                $this->storeData($obj);
            }
        });
        $this->saveProcess();
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
        $result['average_item_price'] = (float) number_format((array_sum($result['item_prices'])/count($result['item_prices'])),2,',','.');
        unset($result['item_prices']);
        if($result['total_order_value'] > 0 ) $this->store[]=$result;
    }

    function saveProcess(){
        $reports = $this->store;
        $report_limit = $_ENV['REPORT_CHECK_LIMIT'];
        $report_count = count($reports);
        $limit = ceil($report_count/$report_limit);
        for($i=0; $i < $limit; $i++){
            $arr = array_slice($reports, (int) ($i * $report_limit), $report_limit);
            $ids = $newArr = [];
            foreach($arr as $report){
                $ids[] = $report['order_id'];
                $newArr[$report['order_id']] = $report;
            }
            $existingReports = $this->batchCheckOrder($ids);
            if(!$existingReports) $this->batchSaveReports($arr);
            if($existingReports){
                foreach($existingReports as $exist){
                    unset($newArr[$exist->getOrderId()]);
                }
                $this->batchSaveReports($arr);
            }
        }

    }

    function batchCheckOrder($data){
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery(
            'SELECT r
            FROM App\Entity\Report r
            WHERE r.order_id IN (:ids)
            ORDER BY r.order_id ASC'
        )->setParameter('ids',$data);

        return $query->getResult();
    }

    function batchSaveReports($datas){
        $entityManager = $this->getEntityManager();
        foreach($datas as $data) {
            $reports = new Report;
            $reports->setOrderId($data['order_id']);
            $reports->setOrderDateTime(new \DateTime($data['order_datetime']));
            $reports->setTotalOrderValue($data['total_order_value']);
            $reports->setAverageUnitPrice($data['average_item_price']);
            $reports->setDistinctUnitCount($data['distinct_unit_count']);
            $reports->setTotalUnitsCount($data['total_units_count']);
            $reports->setCustomerState($data['customer_state']);
            $entityManager->persist($reports);
        }
        $entityManager->flush();
    }

    function showReports(){
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery(
            'SELECT r
            FROM App\Entity\Report r
            ORDER BY r.order_id ASC'
        );

        return $query->getResult();
    }

    public function sendNotification($email,$file = null)
    {
        $email = (new Email())
            ->from('test@gudangartdesign.xyz')
            ->to($email)
            ->subject('Catch Order Report')
            ->text('Sending emails is fun again!')
            ->html('<p>See Twig integration for better HTML integration!</p>');
        if($file){
            $email->attachFromPath($file);
        }
        $this->mailer->send($email);
    }
}