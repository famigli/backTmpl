<?php

namespace App\Entity;

use App\Repository\RepartoRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=RepartoRepository::class)
 */
class Reparto
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $descrizione;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDescrizione(): ?string
    {
        return $this->descrizione;
    }

    public function setDescrizione(string $descrizione): self
    {
        $this->descrizione = $descrizione;

        return $this;
    }
}
