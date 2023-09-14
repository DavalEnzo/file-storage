<?php

namespace App\Repository;

use App\Entity\File;
use App\Entity\Storage;
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

    public function getTotalFilesByUserId(int $id): ?int
    {
        return $this->createQueryBuilder('f')
            ->select('COUNT(f.id)')
            ->join('f.storage', 's')
            ->join('s.user', 'u')
            ->where('u.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function getFilesFromCriteria(Storage $storage, array $criteria = [])
    {
        $query =  $this->createQueryBuilder('f')
            ->join("f.storage", "s")
            ->where("s = :storage")
            ->setParameter('storage', $storage);

        if (array_key_exists("name", $criteria) && $criteria["name"] !== null) {
            $query
                ->andWhere("f.name LIKE :name")
                ->setParameter('name', '%'.$criteria["name"].'%');
        }

        if (array_key_exists("format", $criteria) && $criteria["format"] !== null) {
            $query
                ->andWhere("f.format LIKE :format")
                ->setParameter('format', '%'.$criteria["format"].'%');
        }

        if (array_key_exists("size_min", $criteria) && $criteria["size_min"] !== null) {
            $query
                ->andWhere("f.size >= :size_min * 1000") // car on stack en O mais la valeur est en Ko
                ->setParameter('size_min', $criteria["size_min"]);
        }

        if (array_key_exists("size_max", $criteria) && $criteria["size_max"] !== null) {
            $query
                ->andWhere("f.size <= :size_max * 1000") // car on stack en O mais la valeur est en Ko
                ->setParameter('size_max', $criteria["size_max"]);
        }

        if (array_key_exists("date_min", $criteria) && $criteria["date_min"] !== null) {
            $query
                ->andWhere("f.upload_date >= :date_min")
                ->setParameter('date_min', $criteria["date_min"]);
        }

        if (array_key_exists("date_max", $criteria) && $criteria["date_max"] !== null) {
            $query
                ->andWhere("f.upload_date <= :date_max")
                ->setParameter('date_max', $criteria["date_max"]->add(new \DateInterval('P1D'))); // +1 jour sinon la date devenait incorrecte
        }

        if (array_key_exists("order_size", $criteria) && $criteria["order_size"] !== null) {
            $query
                ->addOrderBy("f.size", $criteria['order_size']);
        }

        if (array_key_exists("order_date", $criteria) && $criteria["order_date"] !== null) {
            $query
                ->addOrderBy("f.upload_date", $criteria["order_date"]);
        }

        return $query->getQuery()->getResult();
    }
}
