<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_EMAIL', fields: ['email'])]
#[UniqueEntity(fields: ['email'], message: 'There is already an account with this email')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180)]
    private ?string $email = null;

    /**
     * @var list<string> The user roles
     */
    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;

    /**
     * @var Collection<int, Comment>
     */
    #[ORM\OneToMany(targetEntity: Comment::class, mappedBy: 'createdBy')]
    private Collection $comments;

    /**
     * @var Collection<int, Snowtrick>
     */
    #[ORM\OneToMany(targetEntity: Snowtrick::class, mappedBy: 'createdBy')]
    private Collection $snowtricks;

    #[ORM\Column(length: 255)]
    private ?string $firstname = null;

    #[ORM\Column(length: 255)]
    private ?string $lastname = null;

    /**
     * @var Collection<int, File>
     */
    #[ORM\OneToMany(targetEntity: File::class, mappedBy: 'createdBy')]
    private Collection $uploadedFiles;

    /**
     * @var Collection<int, File>
     */
    #[ORM\OneToMany(targetEntity: File::class, mappedBy: 'editedBy')]
    private Collection $editedFiles;

    #[ORM\Column(options: ['default' => false])]
    private bool $isVerified = false;

	public function __toString(): string
	{
		return $this->firstname . ' ' . $this->lastname;
	}

	public function __construct()
    {
        $this->comments = new ArrayCollection();
        $this->snowtricks = new ArrayCollection();
        $this->uploadedFiles = new ArrayCollection();
        $this->editedFiles = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
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
     * @see UserInterface
     *
     * @return list<string>
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    /**
     * @param list<string> $roles
     */
    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    /**
     * @return Collection<int, Comment>
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function addComment(Comment $comment): static
    {
        if (!$this->comments->contains($comment)) {
            $this->comments->add($comment);
            $comment->setCreatedBy($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): static
    {
        if ($this->comments->removeElement($comment)) {
            // set the owning side to null (unless already changed)
            if ($comment->getCreatedBy() === $this) {
                $comment->setCreatedBy(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Snowtrick>
     */
    public function getSnowtricks(): Collection
    {
        return $this->snowtricks;
    }

    public function addSnowtrick(Snowtrick $snowtrick): static
    {
        if (!$this->snowtricks->contains($snowtrick)) {
            $this->snowtricks->add($snowtrick);
            $snowtrick->setCreatedBy($this);
        }

        return $this;
    }

    public function removeSnowtrick(Snowtrick $snowtrick): static
    {
        if ($this->snowtricks->removeElement($snowtrick)) {
            // set the owning side to null (unless already changed)
            if ($snowtrick->getCreatedBy() === $this) {
                $snowtrick->setCreatedBy(null);
            }
        }

        return $this;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(string $firstname): static
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(string $lastname): static
    {
        $this->lastname = $lastname;

        return $this;
    }

    /**
     * @return Collection<int, File>
     */
    public function getUploadedFiles(): Collection
    {
        return $this->uploadedFiles;
    }

    public function addUploadedFile(File $uploadedFile): static
    {
        if (!$this->uploadedFiles->contains($uploadedFile)) {
            $this->uploadedFiles->add($uploadedFile);
            $uploadedFile->setCreatedBy($this);
        }

        return $this;
    }

    public function removeUploadedFile(File $uploadedFile): static
    {
        if ($this->uploadedFiles->removeElement($uploadedFile)) {
            // set the owning side to null (unless already changed)
            if ($uploadedFile->getCreatedBy() === $this) {
                $uploadedFile->setCreatedBy(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, File>
     */
    public function getEditedFiles(): Collection
    {
        return $this->editedFiles;
    }

    public function addEditedFile(File $editedFile): static
    {
        if (!$this->editedFiles->contains($editedFile)) {
            $this->editedFiles->add($editedFile);
            $editedFile->setEditedBy($this);
        }

        return $this;
    }

    public function removeEditedFile(File $editedFile): static
    {
        if ($this->editedFiles->removeElement($editedFile)) {
            // set the owning side to null (unless already changed)
            if ($editedFile->getEditedBy() === $this) {
                $editedFile->setEditedBy(null);
            }
        }

        return $this;
    }

    public function isVerified(): bool
    {
        return $this->isVerified;
    }

    public function setIsVerified(bool $isVerified): static
    {
        $this->isVerified = $isVerified;

        return $this;
    }
}
