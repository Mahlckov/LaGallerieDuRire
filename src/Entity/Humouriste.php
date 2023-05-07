<?php

namespace App\Entity;

use App\Repository\HumouristeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=HumouristeRepository::class)
 * @UniqueEntity(fields={"email"}, message="Cet email est déjà utilisé pour un autre compte")
 * @UniqueEntity(fields={"pseudo"}, message="Ce pseudo est déjà utilisé pour un autre compte")
 */
class Humouriste implements UserInterface, PasswordAuthenticatedUserInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     * @Assert\NotBlank(message="L'email est obligatoire")
     * @Assert\Email(message="Votre email n'est pas valide !")
     */
    private $email;

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     * @Assert\Length(
     *     min=3,
     *     minMessage="Minimum 3 caractères s'il vous plait!",
     * )
     */
    private $password;

    /**
     * @ORM\Column(type="string", length=50)
     * @Assert\NotBlank(message="Le pseudo est obligatoire!")
     * @Assert\Length(
     *     min=3,
     *     max=50,
     *     minMessage="Minimum 3 caractères s'il vous plait!",
     *     maxMessage="Maximum 50 caractères s'il vous plait!"
     * )
     * @Assert\Regex(
     *     pattern="/^[a-z0-9_-]+$/i",
     *     message="Votre pseudo ne doit comporter que des lettres, nombres, underscores et tirets!"
     * )
     */
    private $pseudo;

    /**
     * @ORM\Column(type="string", length=50)
     * @Assert\Length(
     *     min=3,
     *     max=50,
     *     minMessage="Minimum 3 caractères s'il vous plait!",
     *     maxMessage="Maximum 50 caractères s'il vous plait!"
     * )
     * @Assert\Regex(
     *     pattern="/^[A-Za-zéèêë\-]+( [A-Za-zéèêë\-]+)?$/",
     *     message="Le nom saisi ne respecte pas les conventions de langage"
     * )
     */
    private $nom;

    /**
     * @ORM\Column(type="string", length=50)
     * @Assert\NotBlank(message="Le prénom est obligatoire")
     * @Assert\Length(
     *     min=3,
     *     max=50,
     *     minMessage="Minimum 3 caractères s'il vous plait!",
     *     maxMessage="Maximum 50 caractères s'il vous plait!"
     * )
     * @Assert\Regex(
     *     pattern="/^[A-Za-zéèêë\-]+$/",
     *     message="Le prénom saisi ne respecte pas les conventions de langage"
     * )
     */
    private $prenom;

    /**
     * @ORM\Column(type="boolean", options={"default":"1"})
     */
    private $actif;

    /**
     * @ORM\Column(type="boolean", options={"default":"0"})
     */
    private $admin;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $nomImage;

    /**
     * @ORM\OneToMany(targetEntity=Blague::class, mappedBy="humouriste")
     */
    private $mesBlagues;

    public function __construct()
    {
        $this->mesBlagues = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @deprecated since Symfony 5.3, use getUserIdentifier instead
     */
    public function getUsername(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = [];
        if ($this->isAdmin()) {
            $roles[] = 'ROLE_ADMIN';
        } else {
            // guarantee every user at least has ROLE_USER
            $roles[] = 'ROLE_USER';
        }
        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Returning a salt is only needed, if you are not using a modern
     * hashing algorithm (e.g. bcrypt or sodium) in your security.yaml.
     *
     * @see UserInterface
     */
    public function getSalt(): ?string
    {
        return null;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getPseudo(): ?string
    {
        return $this->pseudo;
    }

    public function setPseudo(string $pseudo): self
    {
        $this->pseudo = $pseudo;

        return $this;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): self
    {
        $this->prenom = $prenom;

        return $this;
    }

    public function isActif(): ?bool
    {
        return $this->actif;
    }

    public function setActif(bool $actif): self
    {
        $this->actif = $actif;

        return $this;
    }

    public function isAdmin(): ?bool
    {
        return $this->admin;
    }

    public function setAdmin(bool $admin): self
    {
        $this->admin = $admin;

        return $this;
    }

    public function getNomImage(): ?string
    {
        return $this->nomImage;
    }

    public function setNomImage(?string $nomImage): self
    {
        $this->nomImage = $nomImage;

        return $this;
    }

    /**
     * @return Collection<int, Blague>
     */
    public function getMesBlagues(): Collection
    {
        return $this->mesBlagues;
    }

    public function addMesBlague(Blague $mesBlague): self
    {
        if (!$this->mesBlagues->contains($mesBlague)) {
            $this->mesBlagues[] = $mesBlague;
            $mesBlague->setHumouriste($this);
        }

        return $this;
    }

    public function removeMesBlague(Blague $mesBlague): self
    {
        if ($this->mesBlagues->removeElement($mesBlague)) {
            // set the owning side to null (unless already changed)
            if ($mesBlague->getHumouriste() === $this) {
                $mesBlague->setHumouriste(null);
            }
        }

        return $this;
    }
}
