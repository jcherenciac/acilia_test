<?php

namespace App\Repository;

use App\Entity\Category;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * @method Category|null find($id, $lockMode = null, $lockVersion = null)
 * @method Category|null findOneBy(array $criteria, array $orderBy = null)
 * @method Category[]    findAll()
 * @method Category[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 * @property EntityManagerInterface manager
 */
class CategoryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry, EntityManagerInterface $manager)
    {
        parent::__construct($registry, Category::class);
        $this->manager = $manager;
    }

    /**
     * @param $data
     */
    public function add($data)
    {

        $category = new Category();
        $category->setName($data['name']);
        $category->setDescription($data['description']);
        $this->manager->persist($category);
        $this->manager->flush();
    }

    public function update(Category $category, $data)
    {
        if (!empty($data['name'])) {
            $category->setName($data['name']);
        }

        if (!empty($data['description'])) {
            $category->setDescription($data['description']);
        }

        if (!empty($data)) {
            $this->manager->persist($category);
            $this->manager->flush();
        }

    }

    public function remove(Category $category)
    {
        $this->manager->remove($category);
        $this->manager->flush();
    }

    /**
     * @return mixed
     */
    public function getAll()
    {
        return $this->createQueryBuilder('c')
            ->getQuery()
            ->getArrayResult();
    }
}
