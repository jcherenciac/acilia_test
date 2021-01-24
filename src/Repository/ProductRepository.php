<?php

namespace App\Repository;

use App\Entity\Category;
use App\Entity\Product;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * @method Product|null find($id, $lockMode = null, $lockVersion = null)
 * @method Product|null findOneBy(array $criteria, array $orderBy = null)
 * @method Product[]    findAll()
 * @method Product[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 * @property EntityManagerInterface manager
 */
class ProductRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry, EntityManagerInterface $manager)
    {
        parent::__construct($registry, Product::class);
        $this->manager = $manager;
    }

    public function getAll()
    {
        return $this->createQueryBuilder('p')
            ->getQuery()
            ->getArrayResult();
    }

    public function add($data)
    {
        $category = $this->manager
            ->getRepository(Category::class)
            ->find($data['category']);

        if (!$category) {
            throw new NotFoundHttpException('Category not found!');
        }

        $product = new Product();
        $product->setName($data['name']);
        /** @var Category $category */
        $product->setCategory($category);
        $product->setPrice($data['price']);
        try {
            $product->setCurrency($data['currency']);
        } catch (\InvalidArgumentException $invalidArgumentException) {
            throw $invalidArgumentException;
        }
        $product->setFeatured(is_bool($data['featured']) ? false : $data['featured']);

        $this->manager->persist($product);
        $this->manager->flush();
    }

    public function getFeatured()
    {
        $qb =$this->createQueryBuilder('p')
            ->select('p.id,p.name,p.price,p.currency','c.name')
            ->innerJoin('p.category','c')
            ->where('p.featured = 1')
            ;
        return $qb->getQuery()->getArrayResult();
    }
}
