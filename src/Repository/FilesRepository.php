<?php

namespace App\Repository;

use App\Entity\File;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<File>
 *
 * @method File|null find($id, $lockMode = null, $lockVersion = null)
 * @method File|null findOneBy(array $criteria, array $orderBy = null)
 * @method File[]    findAll()
 * @method File[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FilesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, File::class);
    }

    public function save(File $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(File $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * nombre de fichier uploadé aujourd'hui
     * @throws NonUniqueResultException
     * @return int
     */
    public function getTodayFiles(): int
    {
        return $this->createQueryBuilder('f')
            ->select('COUNT(f.id)')
            ->where('f.upload_date >= :today')
            ->setParameter('today', new \DateTime('today'))
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * nombre total de fichier uploadé
     * @throws NonUniqueResultException
     * @return int
     */
    public function getTotalFiles(): int
    {
        return $this->createQueryBuilder('f')
            ->select('COUNT(f.id)')
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function getTotalFilesByUser(User $user): int
    {
        return $this->createQueryBuilder('f')
            ->select('COUNT(f.id)')
            ->join('f.storage', 's')
            ->where('s.user = :user')
            ->setParameter('user', $user)
            ->getQuery()
            ->getSingleScalarResult();
    }
}
