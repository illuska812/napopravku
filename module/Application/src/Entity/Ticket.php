<?php

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity(repositoryClass="\Application\Repository\TicketRepository")
 * @ORM\Table(name="tickets")
 */
class Ticket {

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(name="ticket_id")   
     */
    protected $ticketId;

    /**
     * @ORM\Column(name="doctor_id")
     */
    protected $doctorId;

    /**
     * @ORM\ManyToOne(targetEntity="\Application\Entity\Doctor", inversedBy="tickets")
     * @ORM\JoinColumn(name="doctor_id", referencedColumnName="doctor_id")
     */
    protected $doctor;

    /**
     * @ORM\Column(name="start_datetime")  
     */
    protected $startDatetime;

    /**
     * @ORM\Column(name="end_datetime")
     */
    protected $endDatetime;

    /**
     * @ORM\Column(name="user_id")
     */
    protected $userId;

    /*
     * @return \Application\Entity\Doctor
     */
    public function getDoctor() 
    {
        return $this->doctor;
    }

    /**
     * @param \Application\Entity\Doctor $doctor
     */
    public function setDoctor($doctor) 
    {
        $this->doctor = $doctor;
        $doctor->addTicket($this);
    }

    public function setUserId($userId)
    {
        $this->userId = $userId;
        return $this;
    }

    public function getId()
    {
        return $this->ticketId;
    }

    public function getStartDateTime()
    {
        return $this->startDatetime;
    }

    public function getEndDateTime()
    {
        return $this->endDatetime;
    }

    public function isOccupied()
    {
        if(!empty($this->userId)){
            return true;
        } else {
            return false;
        }
    }

    /**
     * @param [] $params - ['id', 'dates', 'user', 'doctor']
     * @return []
     */
    public function toJsonArray($params = ['id', 'dates', 'user'], $userId)
    {
        $title = '';
        $selfTicket = false;
        $occupied = false;
        if($this->isOccupied()){
            $occupied = true;
            if(!empty($userId) && (int) $this->userId === (int) $userId){
                $title = 'Ваш номерок к врачу';
                $selfTicket = true;
            } else {
                $title = 'занято';
            }
        } else {
            $title = 'свободно';
        }
        $result = [
            'id' => $this->getId(),
            'title' => $title,
            'occupied' => $occupied,
            'selfTicket' => $selfTicket,
            'allDay' => false,
        ];
        if(in_array('dates', $params)){
            $result['startTime'] = $this->getStartDateTime();
            $result['endTime'] = $this->getEndDateTime();
        }
        if(in_array('user', $params)){
            $result['user'] = $this->userId;
        }
        if(in_array('doctor', $params)){
            $result['doctor'] = $this->getDoctor()->toJsonArray();
        }
        return $result;
    }
}
