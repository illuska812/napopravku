<?php
/**
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2016 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\View\Model\JsonModel;
use Application\Entity\Doctor;

class DoctorController extends AbstractActionController
{
    /**
     * @var Doctrine\ORM\EntityManager 
     */
    private $entityManager;

    public function __construct($entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function indexAction()
    {
        return new ViewModel();
    }
    
    public function getDoctorsListAction()
    {
        $result = [];
        /* @var $doctors \Application\Entity\Doctor[] */
        $doctors = $this->entityManager->getRepository(Doctor::class)->findAll();
        /* @var $doctor Doctor */
        foreach ($doctors as $doctor) {
            $result[] = $doctor->toJsonArray();
        }
        return new JsonModel([
            'status' => 'success',
            'data' => $result
        ]);
    }
}
