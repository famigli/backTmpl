<?php

namespace App\Entity;

use App\Repository\Rfc249OutRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=Rfc249OutRepository::class)
 */
class Rfc249Out
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="datetime")
     */
    private $data;

    /**
     * @ORM\Column(type="text")
     */
    private $messaggio;

    /**
     * @ORM\Column(type="string", length=2)
     */
    private $stato;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getData(): ?\DateTimeInterface
    {
        return $this->data;
    }

    public function setData(\DateTimeInterface $data): self
    {
        $this->data = $data;

        return $this;
    }

    public function getMessaggio(): ?string
    {
        return $this->messaggio;
    }

    public function setMessaggio(string $messaggio): self
    {
        $this->messaggio = $messaggio;

        return $this;
    }

    public function getStato(): ?string
    {
        return $this->stato;
    }

    public function setStato(string $stato): self
    {
        $this->stato = $stato;

        return $this;
    }
}
