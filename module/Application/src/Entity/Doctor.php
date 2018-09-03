<?php

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="doctors")
 */
class Doctor {

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(name="doctor_id")   
     */
    protected $doctorId;

    /**
     * @ORM\Column(name="first_name")  
     */
    protected $firstName;

    /**
     * @ORM\Column(name="middle_name")  
     */
    protected $middleName;

    /**
     * @ORM\Column(name="last_name")
     */
    protected $lastName;

    /**
     * @ORM\Column(name="info")
     */
    protected $info;

    /**
     * @ORM\Column(name="active")
     */
    protected $active;

    public function getId()
    {
        return $this->doctorId;
    }

    public function setId($id)
    {
        $this->doctorId = $id;
    }

    public function getTitle()
    {
        return $this->lastName . ' ' . $this->firstName . ' ' . $this->middleName;
    }

    public function getLastName()
    {
        return $this->lastName;
    }

    public function getFirstName()
    {
        return $this->firstName;
    }

    public function getMiddleName()
    {
        return $this->middleName;
    }

    public function getInfo()
    {
        return $this->info;
    }

    public function toJsonArray($params = ['id', 'title', 'explode_title', 'info'])
    {
        $result = [
            'id' => $this->getId(),
        ];
        if(in_array('title', $params)){
            $result['title'] = $this->getTitle();
        }
        if(in_array('explode_title', $params)){
            $result['explode_title'] = [
                'first_name' => $this->getFirstName(),
                'middle_name' => $this->getMiddleName(),
                'last_name' => $this->getLastName(),
            ];
        }
        if(in_array('info', $params)){
            $result['info'] = $this->getInfo();
        }
        return $result;
    }

}
