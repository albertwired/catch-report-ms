<?php
namespace App\Entity;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
/**
 * @ORM\Entity
 * @ORM\Table(name="report")
 */
class Report {
  /**
   * @ORM\Column(type="integer")
   * @ORM\Id
   * @ORM\GeneratedValue(strategy="AUTO")
   */
  private $id;
  /**
   * @ORM\Column(type="integer")
   * @Assert\NotBlank()
   *
   */
  private $order_id;
  /**
   * @ORM\Column(type="datetime")
   * @Assert\NotBlank()
   */
  private $order_datetime;
  /**
   * @ORM\Column(type="decimal", precision=15, scale=2)
   * @Assert\NotBlank()
   */
  private $total_order_value;
  /**
   * @ORM\Column(type="decimal", precision=15, scale=2)
   * @Assert\NotBlank()
   */
  private $average_unit_price;
  /**
   * @ORM\Column(type="integer")
   * @Assert\NotBlank()
   */
  private $distinct_unit_count;
  /**
   * @ORM\Column(type="integer")
   * @Assert\NotBlank()
   */
  private $total_units_count;
  /**
   * @ORM\Column(type="string", length=30)
   * @Assert\NotBlank()
   */
  private $customer_state;
  /**
   * @return mixed
   */
  public function getId()
  {
    return $this->id;
  }
  /**
   * @param mixed $id
   */
  public function setId($id)
  {
    $this->id = $id;
  }
  /**
   * @return mixed
   */
  public function getOrderId()
  {
    return $this->order_id;
  }
  /**
   * @param mixed $name
   */
  public function setOrderId($order_id)
  {
    $this->order_id = $order_id;
  }
  /**
   * @return mixed
   */
  public function getOrderDateTime()
  {
    return $this->order_datetime;
  }
  /**
   * @param mixed $name
   */
  public function setOrderDateTime($order_datetime)
  {
    $this->order_datetime = $order_datetime;
  }
  /**
   * @return mixed
   */
  public function getTotalOrderValue()
  {
    return $this->total_order_value;
  }
  /**
   * @param mixed $name
   */
  public function setTotalOrderValue($total_order_value)
  {
    $this->total_order_value = $total_order_value;
  }
  /**
   * @return mixed
   */
  public function getAverageUnitPrice()
  {
    return $this->average_unit_price;
  }
  /**
   * @param mixed $name
   */
  public function setAverageUnitPrice($average_unit_price)
  {
    $this->average_unit_price = $average_unit_price;
  }
  /**
   * @return mixed
   */
  public function getDistinctUnitCount()
  {
    return $this->distinct_unit_count;
  }
  /**
   * @param mixed $name
   */
  public function setDistinctUnitCount($distinct_unit_count)
  {
    $this->distinct_unit_count = $distinct_unit_count;
  }
  /**
   * @return mixed
   */
  public function getTotalUnitsCount()
  {
    return $this->total_units_count;
  }
  /**
   * @param mixed $name
   */
  public function setTotalUnitsCount($total_units_count)
  {
    $this->total_units_count = $total_units_count;
  }
  /**
   * @return mixed
   */
  public function getCustomerState()
  {
    return $this->customer_state;
  }
  /**
   * @param mixed $name
   */
  public function setCustomerState($customer_state)
  {
    $this->customer_state = $customer_state;
  }
}