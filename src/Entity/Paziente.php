<?php

namespace App\Entity;
use App\Repository\PazienteRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\Ignore;
use Symfony\Component\Serializer\Annotation\Groups;
/**
 * @ORM\Entity(repositoryClass=PazienteRepository::class)
 */
class Paziente
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"a", "gruppo2"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=50)
     * @Groups({"a"})
     */
    private $cognome;

    /**
     * @ORM\Column(type="string", length=50)
     * @Ignore()
     */
    private $nome;

    /**
     * @ORM\Column(type="date")
     * @Groups({"a"})
     */
    private $data_nascita;

    /**
     * @ORM\Column(type="string", length=1)
     * @Assert\Choice(choices = { "M", "F" }, message = "Il campo sesso ammette i valori M o F")
     * @Groups({"a"})
     */
    private $sesso;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $telefono;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $indirizzoResidenza;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $comune_residenza;

    /**
     * @ORM\Column(type="boolean")
     */
    private $is_ricoverato;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCognome(): ?string
    {
        return $this->cognome;
    }

    public function setCognome(string $cognome): self
    {
        $this->cognome = $cognome;

        return $this;
    }

    public function getNome(): ?string
    {
        return $this->nome;
    }

    public function setNome(string $nome): self
    {
        $this->nome = $nome;

        return $this;
    }

    public function getDataNascita(): ?\DateTimeInterface
    {
        return $this->data_nascita;
    }

    public function setDataNascita(\DateTimeInterface $data_nascita): self
    {
        $this->data_nascita = $data_nascita;

        return $this;
    }

    public function getSesso(): ?string
    {
        return $this->sesso;
    }

    public function setSesso(string $sesso): self
    {
        $this->sesso = $sesso;

        return $this;
    }

    public function getTelefono(): ?string
    {
        return $this->telefono;
    }

    public function setTelefono(?string $telefono): self
    {
        $this->telefono = $telefono;

        return $this;
    }

    public function getIndirizzoResidenza(): ?string
    {
        return $this->indirizzoResidenza;
    }

    public function setIndirizzoResidenza(?string $indirizzoResidenza): self
    {
        $this->indirizzoResidenza = $indirizzoResidenza;

        return $this;
    }

    public function getComuneResidenza(): ?string
    {
        return $this->comune_residenza;
    }

    public function setComuneResidenza(?string $comune_residenza): self
    {
        $this->comune_residenza = $comune_residenza;

        return $this;
    }

    public function getIsRicoverato(): ?bool
    {
        return $this->is_ricoverato;
    }

    public function setIsRicoverato(bool $is_ricoverato): self
    {
        $this->is_ricoverato = $is_ricoverato;

        return $this;
    }
}
