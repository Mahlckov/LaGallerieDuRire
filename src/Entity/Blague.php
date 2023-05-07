<?php

namespace App\Entity;

use App\Repository\BlagueRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=BlagueRepository::class)
 */
class Blague
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=50)
     * @Assert\NotBlank(message="Le titre de la blague doit être précisé")
     * @Assert\Length(
     *     min=3,
     *     max=50,
     *     minMessage="Minimum 3 caractères s'il vous plait!",
     *     maxMessage="Maximum 50 caractères s'il vous plait!"
     * )
     */
    private $libelle;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\Length(
     *     max=50,
     *     maxMessage="Maximum 50 caractères s'il vous plait!"
     * )
     */
    private $description;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $nomMeme;

    /**
     * @ORM\ManyToOne(targetEntity=Humouriste::class, inversedBy="mesBlagues")
     */
    private $humouriste;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLibelle(): ?string
    {
        return $this->libelle;
    }

    public function setLibelle(string $libelle): self
    {
        $this->libelle = $libelle;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getNomMeme(): ?string
    {
        return $this->nomMeme;
    }

    public function setNomMeme(?string $nomMeme): self
    {
        $this->nomMeme = $nomMeme;

        return $this;
    }

    public function getHumouriste(): ?Humouriste
    {
        return $this->humouriste;
    }

    public function setHumouriste(?Humouriste $humouriste): self
    {
        $this->humouriste = $humouriste;

        return $this;
    }
}
