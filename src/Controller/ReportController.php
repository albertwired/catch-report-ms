<?php
namespace App\Controller;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Swagger\Annotations as SWG;
use App\Repository\ReportRepository;
use App\Entity\Report;
use App\Requests\ReportGenerateRequest;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Encoder\CsvEncoder;
use Symfony\Component\Serializer\Encoder\YamlEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Filesystem\Filesystem;
/**
 * Report controller.
 * @Route("/api", name="api_")
 */
class ReportController extends FOSRestController
{
  /**
   * Lists all Report.
   * @Rest\Get("/reports")
   *
   * @return Response
   */
  public function getReportAction(ReportRepository $reportRepository)
  {
    $reports = $reportRepository->showReports();
    return $reports;
    return $this->handleView($this->view($reports));
  }
  /**
   * Stream and genrate order Report.
   * @Rest\Get("/download-report")
   * @SWG\Response(
   *    response=200,
   *    description="Returns CSV, JSON, XML, and YAML files"
   * )
   * @SWG\Parameter(
   *    name="result-type",
   *    in="query",
   *    type="string",
   *    description="The file type to be downloaded (csv,json, xml,yaml)"
   * )
   * @SWG\Parameter(
   *    name="email-to",
   *    in="query",
   *    type="string",
   *    description="If report-to set to email then specify your email"
   * )
   *
   * @return Response
   */
  public function getReportStreamAction(ReportRepository $reportRepository,ReportGenerateRequest $request)
  {
    $data = $request->query->all();
    $acceptedResult = ['json','csv','yaml','xml'];
    $result = [
        'message' => 'Unsupported download format'
    ];
    if(in_array($data['result-type'], $acceptedResult)){
        $reports = $reportRepository->streamS3();
        $serializerOption = [
            new JsonEncoder(),
            new CsvEncoder(),
            new XmlEncoder(),
            new YamlEncoder()
        ];
        $serializer = new Serializer([new ObjectNormalizer()],$serializerOption);
        $result = $serializer->serialize($reports,$data['result-type']);
        
        $response = new Response($result);
        $response->headers->set('Content-Type', 'text/'.$data['result-type']);
        $response->headers->set('Content-Disposition', 'attachment; filename="out.'.$data['result-type'].'"');
        if(isset($data['email-to']) && $data['email-to']){
          $filesystem = new Filesystem();
          $filesystem->mkdir( dirname(__DIR__).'/tmp');
          $filesystem->touch( dirname(__DIR__).'/tmp/out.'.$data['result-type']);
          $filesystem->appendToFile(dirname(__DIR__).'/tmp/out.'.$data['result-type'],$result);
          $reportRepository->sendNotification($data['email-to'],dirname(__DIR__).'/tmp/out.'.$data['result-type']);
          $filesystem->remove($data['email-to'],dirname(__DIR__).'/tmp/out.'.$data['result-type']);
          return ['message'=>'report has been sent to email'];
        }
        return $response;
    }else{
        return $result;
        return $this->handleView($this->view($result));
    }
    
  }

}