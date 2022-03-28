<?php

namespace App\DataPersister;

use ApiPlatform\Core\DataPersister\DataPersisterInterface;
use App\Entity\BlogPost;
use Doctrine\ORM\EntityManagerInterface;

class PostPersister implements DataPersisterInterface
{
    protected $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function supports($data): bool
    {
        // TODO: Implement supports() method.
        return $data instanceof BlogPost;
    }

    public function persist($data): void
    {
        // TODO: Implement persist() method.
        $data->setCreatedAt(new \DateTimeImmutable());

        $this->em->persist($data);
        $this->em->flush();
    }

    public function remove($data)
    {
        // TODO: Implement remove() method.
        $this->em->remove($data);
        $this->em->flush();
    }
}