<?php
namespace Application\Repository;
use Doctrine\ORM\EntityRepository;
use Application\Entity\Ticket;
use DateTime;

class TicketRepository extends EntityRepository
{
    /* @var $doctor \Application\Entity\Doctor */
    public function findTicketsByDoctor($doctor, $fromDate = null, $toDate = null)
    {
        $doctorId = $doctor->getId();
        $entityManager = $this->getEntityManager();
        $queryBuilder = $entityManager->createQueryBuilder();
        $queryBuilder->select('t')
            ->from(Ticket::class, 't')
            ->where('t.doctorId = ?1')
            ->orderBy('t.startDatetime')
            ->setParameter('1', $doctorId);
        if($fromDate === null || !($fromDate instanceof Datetime)){
            $fromDate = new DateTime();
        }
        if($toDate === null || !($toDate instanceof Datetime)){
            $toDate = new DateTime("+40 days");
        }
        $queryBuilder->andWhere($queryBuilder->expr()->gte('t.startDatetime', '?2'))
                ->andWhere($queryBuilder->expr()->lte('t.startDatetime', '?3'))                    
                ->setParameter('2', $fromDate->format('Y-m-d H:i:s'))
                ->setParameter('3', $toDate->format('Y-m-d H:i:s'));
        return $queryBuilder->getQuery();
    }
}