<?php
/**
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2016 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\JsonModel;
use Application\Entity\Doctor;
use Application\Entity\Ticket;

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

    /**
     * получение списка врачей
     * @return JsonModel
     */
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
            'success' => true,
            'list' => $result
        ]);
    }

    /**
     * получение списка номерков
     * @return JsonModel
     */
    public function getDoctorTicketsAction()
    {
        // т.к. авторизацию не делал для данного задания, задаю идентификатор текущего пользователя явно
        $userId = 1;

        $result = [];
        $doctorId = $this->params()->fromQuery('doctor_id', null);
        if(empty($doctorId)){
            return new JsonModel([
                'success' => true,
                'code' => '0x01',
                'message' => "Bad request",
            ]);
        }
        $from = $this->params()->fromQuery('from', null);
        /* @var $doctor \Application\Entity\Doctor */
        $doctor = $this->entityManager->getRepository(Doctor::class)->find($doctorId);
        if(empty($doctor)){
            return new JsonModel([
                'success' => true,
                'code' => '0x01',
                'message' => "Doctor not found",
            ]);
        }
        $fromDate = \DateTime::createFromFormat('Y-m-d', $from);
        $ticketsRepository = $this->entityManager->getRepository(Ticket::class);
        $tickets = $ticketsRepository->findTicketsByDoctor($doctor, $fromDate)->execute();
        /* @var $ticket Ticket */
        foreach ($tickets as $ticket) {
            $result[] = $ticket->toJsonArray(['id', 'dates', 'user'], $userId);
        }
        return new JsonModel([
            'success' => true,
            'tickets' => $result
        ]);
    }
    
    /**
     * НЕпосредственная запись на прием
     */
    public function getTicketAction()
    {
        // т.к. авторизацию не делал для данного задания, задаю идентификатор пользователя явно
        $userId = 1;
        
        $ticketId = $this->params()->fromQuery('ticket_id', null);
        if(empty(ticketId)){
            return new JsonModel([
                'success' => true,
                'code' => '0x01',
                'message' => "Bad request",
            ]);
        }

        /* @var $connection \Doctrine\DBAL\Connection */
        $connection = $this->entityManager->getConnection();
        $connection->beginTransaction();
        try {
            $criteria = [
                'ticketId' => $ticketId, 
                'userId' => '0',
                ];
            /* @var $ticket Ticket */
            $ticket = $this->entityManager->getRepository(Ticket::class)->findOneBy($criteria);
            if(empty($ticket)){
                return new JsonModel([
                    'success' => false,
                    'code' => '0x02',
                    'message' => "Номерок уже занят другим человеком",
                ]);
            }
            $ticket->setUserId($userId);
            // Применяем изменения к базе данных.
            $this->entityManager->flush();
            $connection->commit();
        } catch (Exception $e) {
            $connection->rollback();
            return new JsonModel([
                'success' => false,
                'code' => "0x03"
            ]);
        }
    
        return new JsonModel([
            'success' => true,
            'ticket' => $ticket->toJsonArray(['id', 'dates', 'user'], $userId)
        ]);
    }
}
