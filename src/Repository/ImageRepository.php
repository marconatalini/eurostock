<?php

namespace App\Repository;


use App\Entity\Category;
use App\Entity\Image;
use App\Pagination\Paginator;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Image|null find($id, $lockMode = null, $lockVersion = null)
 * @method Image|null findOneBy(array $criteria, array $orderBy = null)
 * @method Image[]    findAll()
 * @method Image[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ImageRepository extends ServiceEntityRepository
{
    private $tagRepository;
    private $categoryRepository;

    public function __construct(ManagerRegistry $registry, TagRepository $tagRepository, CategoryRepository $categoryRepository)
    {
        $this->tagRepository = $tagRepository;
        $this->categoryRepository = $categoryRepository;

        parent::__construct($registry, Image::class);
    }

    public function findLasts(int $number)
    {
        return $this->createQueryBuilder('i')
            ->orderBy('i.id', 'DESC')
            ->setMaxResults($number)
            ->getQuery()
            ->getResult();
    }

    public function findMostViewed(int $number)
    {
        return $this->createQueryBuilder('i')
            ->orderBy('i.views', 'DESC')
            ->setMaxResults($number)
            ->getQuery()
            ->getResult();
    }

    public function findBySearchQuery(int $page = 1, Array $tags = [], Category $category = null, string $rawQuery, string $uploadFromDate, string $uploadToDate, int $limit = Image::MAX_RESULT)
    {

        $query = $this->sanitizeSearchQuery($rawQuery);
        $searchTerms = $this->extractSearchTerms($query);

        $queryBuilder = $this->createQueryBuilder('i')
            ->addSelect('c', 't')
            ->innerJoin('i.category', 'c')
            ->leftJoin('i.tags', 't')
            ->where('i.uploadAt >= :upFrom')
            ->andwhere('i.uploadAt <= :upTo')
            ->setParameter('upFrom', $uploadFromDate)
            ->setParameter('upTo', $uploadToDate . ' 23:59:59');


        if ($tags !== []) {
            foreach ($tags as $tag) {
                $queryBuilder
                    ->andWhere(':tag MEMBER OF i.tags')
                    ->setParameter('tag', $tag);
            }
        }

        if ($category) {
            $queryBuilder
                ->andWhere('i.category = :category')
                ->setParameter('category', $category);
        }


        if (\count($searchTerms) !== 0) {
            foreach ($searchTerms as $key => $term) {
                $queryBuilder
                    ->andWhere('i.description LIKE :t_' . $key)
                    ->setParameter('t_' . $key, '%' . $term . '%');
            }
        }

        $queryBuilder
            ->orderBy('i.uploadAt', 'DESC')
            ->setMaxResults($limit);

        return (new Paginator($queryBuilder))->paginate($page);
    }


    /**
     * Removes all non-alphanumeric characters except whitespaces.
     */
    private function sanitizeSearchQuery(string $query): string
    {
        return trim(preg_replace('/[[:space:]]+/', ' ', $query));
    }

    /**
     * Splits the search query into terms and removes the ones which are irrelevant.
     */
    private function extractSearchTerms(string $searchQuery): array
    {
        $terms = array_unique(explode(' ', $searchQuery));

        return array_filter($terms, function ($term) {
            return 2 <= mb_strlen($term);
        });
    }

    public function findImagesToDelete()
    {
        return $this->createQueryBuilder('i')
            ->addSelect('c')
            ->innerJoin('i.category', 'c')
            ->where('i.uploadAt < DATE_ADD(CURRENT_DATE(), (-1 * c.days_before_delete), \'DAY\')')
            ->getQuery()
            ->getResult();
    }

    // /**
    //  * @return Image[] Returns an array of Image objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('i')
            ->andWhere('i.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('i.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Image
    {
        return $this->createQueryBuilder('i')
            ->andWhere('i.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
