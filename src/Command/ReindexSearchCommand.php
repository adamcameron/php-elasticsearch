<?php

namespace App\Command;

use App\Entity\Course;
use App\Entity\Department;
use App\Entity\Instructor;
use App\Entity\Institution;
use App\Entity\Student;
use App\Entity\Assignment;
use App\EventListener\SearchIndexer;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

#[AsCommand(
    name: 'search:reindex',
    description: 'Reindex all or specific entities into Elasticsearch'
)]
class ReindexSearchCommand extends Command
{
    private const array ENTITY_MAP = [
        'assignment' => Assignment::class,
        'course' => Course::class,
        'department' => Department::class,
        'instructor' => Instructor::class,
        'institution' => Institution::class,
        'student' => Student::class,
    ];

    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly SearchIndexer $indexer
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument(
            'entity',
            InputArgument::REQUIRED,
            'Entity to reindex (all, student, course, department, instructor, institution, assignment)'
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $entityArg = strtolower($input->getArgument('entity'));

        if ($entityArg === 'all') {
            $entitiesToIndex = self::ENTITY_MAP;
        } elseif (isset(self::ENTITY_MAP[$entityArg])) {
            $entitiesToIndex = [$entityArg => self::ENTITY_MAP[$entityArg]];
        } else {
            $output->writeln('<error>Unknown entity: ' . $entityArg . '</error>');
            return Command::FAILURE;
        }

        foreach ($entitiesToIndex as $name => $class) {
            $output->writeln("Indexing $name...");
            $this->indexEntity($class, $output);
        }

        $output->writeln('<info>Reindexing complete.</info>');

        return Command::SUCCESS;
    }

    private function indexEntity(string $class, OutputInterface $output): void
    {
        $batchSize = 100;
        $repo = $this->em->getRepository($class);
        $qb = $repo->createQueryBuilder('e');
        $count = (int) $qb->select('COUNT(e.id)')->getQuery()->getSingleScalarResult();

        $output->writeln("  Found $count records.");

        for ($i = 0; $i < $count; $i += $batchSize) {
            $qb = $repo
                ->createQueryBuilder('e')
                ->setFirstResult($i)
                ->setMaxResults($batchSize);

            $results = $qb->getQuery()->getResult();

            foreach ($results as $entity) {
                $this->indexer->sync($entity);
            }

            $this->em->clear();
            gc_collect_cycles();
        }
    }
}
