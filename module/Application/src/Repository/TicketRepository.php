<?php
namespace Application\Repository;
use Doctrine\ORM\EntityRepository;
use Application\Entity\Ticket;

class TicketRepository extends EntityRepository
{
    /* @var $doctor \Application\Entity\Doctor */
    public function findTicketsByDoctor($doctor, $fromDate = null)
    {
        $doctorId = $doctor->getId();
        $entityManager = $this->getEntityManager();
        $queryBuilder = $entityManager->createQueryBuilder();
        $queryBuilder->select('t')
            ->from(Ticket::class, 't')
            ->where('t.doctorId = ?1')
            ->orderBy('t.startDatetime')
            ->setParameter('1', $doctorId);
        if($fromDate !== null && $fromDate instanceof \Zend\Db\Sql\Ddl\Column\Datetime){
            $queryBuilder->andWhere('t.startDatetime >= ?2')
                    ->setParameter('2', $fromDate->format('Y-m-d H:i:s'));
        } else {
            $queryBuilder->andWhere('t.startDatetime > CURRENT_TIMESTAMP()');
        }
        return $queryBuilder->getQuery();
    }
}