<?php

namespace Vibby\Bundle\BookingBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use \DateInterval;

/**
 * Vibby\Bundle\BookingBundle\Entity\Event
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Vibby\Bundle\BookingBundle\Entity\EventRepository")
 */
class Event
{
    /**
     * @var integer $id
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var datetime $date_from
     *
     * @ORM\Column(name="date_from", type="datetime")
     */
    private $date_from;

    /**
     * @var datetime $date_to
     *
     * @ORM\Column(name="date_to", type="datetime")
     */
    private $date_to;

    /**
     * @var string $firstname
     *
     * @ORM\Column(name="firstname", type="string", length=255)
     */
    private $firstname;

    /**
     * @var string $lastname
     *
     * @ORM\Column(name="lastname", type="string", length=255)
     */
    private $lastname;

    /**
     * @var string $email
     *
     * @ORM\Column(name="email", type="string", length=255)
     */
    private $email;

    /**
     * @var string $phone
     *
     * @ORM\Column(name="phone", type="string", length=14)
     */
    private $phone;

    /**
     * @var boolean $is_validated
     *
     * @ORM\Column(name="is_validated", type="boolean")
     */
    private $is_validated;

    
    protected $em;    

    public function __construct($em = null)
    {
        $this->is_validated = false;
        $this->em = $em;
    }
    
    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set date_from
     *
     * @param datetime $dateFrom
     * @return Event
     */
    public function setDateFrom($dateFrom)
    {
        $this->date_from = $dateFrom;
        return $this;
    }

    /**
     * Get date_from
     *
     * @return datetime 
     */
    public function getDateFrom()
    {
        return $this->date_from;
    }

    /**
     * Set date_to
     *
     * @param datetime $dateTo
     * @return Event
     */
    public function setDateTo($dateTo)
    {
        $this->date_to = $dateTo;
        return $this;
    }

    /**
     * Get date_to
     *
     * @return datetime 
     */
    public function getDateTo()
    {
        return $this->date_to;
    }

    /**
     * Set is_validated
     *
     * @param boolean $isValidated
     * @return Event
     */
    public function setIsValidated($isValidated)
    {
        $this->is_validated = $isValidated;
        return $this;
    }

    /**
     * Set is_validated to true
     *
     * @return Event
     */
    public function validate()
    {
      
        $this->setIsValidated(true);
        return $this;
    }

        /**
     * Set is_validated to false
     *
     * @return Event
     */
    public function unvalidate()
    {
        $this->setIsValidated(false);
        return $this;
    }

    /**
     * Get is_validated
     *
     * @return boolean 
     */
    public function getIsValidated()
    {
        return $this->is_validated;
    }

    /**
     * Set firstname
     *
     * @param string $firstname
     * @return Event
     */
    public function setFirstname($firstname)
    {
        $this->firstname = $firstname;
        return $this;
    }

    /**
     * Get firstname
     *
     * @return string 
     */
    public function getFirstname()
    {
        return $this->firstname;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->firstname." ".$this->lastname;
    }

    /**
     * Set lastname
     *
     * @param string $lastname
     * @return Event
     */
    public function setLastname($lastname)
    {
        $this->lastname = $lastname;
        return $this;
    }

    /**
     * Get lastname
     *
     * @return string 
     */
    public function getLastname()
    {
        return $this->lastname;
    }

    /**
     * Set email
     *
     * @param string $email
     * @return Event
     */
    public function setEmail($email)
    {
        $this->email = $email;
        return $this;
    }

    /**
     * Get email
     *
     * @return string 
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set phone
     *
     * @param string $phone
     * @return Event
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;
        return $this;
    }

    /**
     * Get phone
     *
     * @return string 
     */
    public function getPhone()
    {
        return $this->phone;
    }
}