<?php
namespace App\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use App\Entity\Report;
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
  public function getReportAction()
  {
    $repository = $this->getDoctrine()->getRepository(Report::class);
    $reports = $repository->findall();
    return $this->handleView($this->view($reports));
  }
}