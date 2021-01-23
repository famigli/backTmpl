<?php

namespace App\Entity;
use App\Repository\RicoveroRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
/**
 * @ORM\Entity(repositoryClass=RicoveroRepository::class)
 */
class Ricovero
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"gruppo1", "gruppo2"})
     */
    private $id;

    /**
     * @ORM\Column(type="date")
     * @Groups({"gruppo1", "gruppo2"})
     */
    private $data;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $reparto;

    /**
     * @ORM\ManyToOne(targetEntity=Paziente::class, fetch="EAGER")
     * @ORM\JoinColumn(nullable=false)
     */
    private $paziente;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $data_dimissione;

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

    public function getReparto(): ?string
    {
        return $this->reparto;
    }

    public function setReparto(string $reparto): self
    {
        $this->reparto = $reparto;

        return $this;
    }

    public function getPaziente(): ?Paziente
    {
        return $this->paziente;
    }

    public function setPaziente(?Paziente $paziente): self
    {
        $this->paziente = $paziente;

        return $this;
    }

    public function getDataDimissione(): ?\DateTimeInterface
    {
        return $this->data_dimissione;
    }

    public function setDataDimissione(?\DateTimeInterface $data_dimissione): self
    {
        $this->data_dimissione = $data_dimissione;

        return $this;
    }
}
