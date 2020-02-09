<?php

namespace App\Command;

use App\Entity\Image;
use App\Repository\ImageRepository;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

use Doctrine\ORM\EntityManagerInterface;
use Liip\ImagineBundle\Imagine\Cache\CacheManager as LiipCacheManager;
use Vich\UploaderBundle\Templating\Helper\UploaderHelper as UploaderHelper;

class EurostockCleanCommand extends Command
{
    protected static $defaultName = 'eurostock:clean';

    private $images;
    private $cacheManager;
    private $uploaderBundle;
    private $objectManager;

    public function __construct(ImageRepository $images, LiipCacheManager $cacheManager, UploaderHelper $uploaderBundle, EntityManagerInterface $objectManager)
    {
        $this->images = $images;
        $this->cacheManager = $cacheManager;
        $this->uploaderBundle = $uploaderBundle;
        $this->objectManager = $objectManager;

        parent::__construct();
    }


    protected function configure()
    {
        $this
            ->setDescription('Cancellazione foto e documenti scaduti.')
            ->setHelp('Con questo comando tutte le immagine di ogni categoria verranno cancellate in base alla loro scadenza. I giorni sono definiti per ogni categoria.')
            ->addArgument('arg1', InputArgument::OPTIONAL, 'Argument description')
            ->addOption('option1', null, InputOption::VALUE_NONE, 'Option description');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $arg1 = $input->getArgument('arg1');


        if ($arg1) {
            $io->note(sprintf('You passed an argument: %s', $arg1));
        }

        if ($input->getOption('option1')) {
            return 0;
        }

        $count = 0;

        /** @var Image $image */
        foreach ($this->images->findImagesToDelete() as $image) {
            $count ++;
            // get the UploaderHelper service...
            $resolvedPath = $this->uploaderBundle->asset($image, 'imageFile');

            $this->cacheManager->remove($resolvedPath, 'homepage_thumb');
            $this->objectManager->remove($image);

            $io->writeln($image->getCategory()->getName() .": ". $image->getImageName());
        }
//        $objectManager->flush();

        if ($count){
            $io->success('Hai cancellato '. $count. ' immagini.');
        }

        return 0;
    }

}
