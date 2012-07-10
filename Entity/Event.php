<?php

namespace Vibby\Bundle\BookingBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

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
     * @var string $title
     *
     * @ORM\Column(name="title", type="string", length=255)
     */
    private $title;

    /**
     * @var boolean $is_validated
     *
     * @ORM\Column(name="is_validated", type="boolean")
     */
    private $is_validated;


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
     * Set title
     *
     * @param string $title
     * @return Event
     */
    public function setTitle($title)
    {
        $this->title = $title;
        return $this;
    }

    /**
     * Get title
     *
     * @return string 
     */
    public function getTitle()
    {
        return $this->title;
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
     * Get is_validated
     *
     * @return boolean 
     */
    public function getIsValidated()
    {
        return $this->is_validated;
    }
}