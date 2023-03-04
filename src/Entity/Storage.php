<?php

namespace App\Entity;

use App\Repository\StorageRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: StorageRepository::class)]
class Storage
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $initial_capacity = null;

    #[ORM\Column]
    private ?int $left_capacity = null;

    #[ORM\OneToOne(inversedBy: 'storage', cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\OneToMany(mappedBy: 'storage', targetEntity: Files::class, orphanRemoval: true)]
    private Collection $files;

    public function __construct()
    {
        $this->files = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getInitialCapacity(): ?int
    {
        return $this->initial_capacity;
    }

    public function setInitialCapacity(int $initial_capacity): self
    {
        $this->initial_capacity = $initial_capacity;

        return $this;
    }

    public function getLeftCapacity(): ?int
    {
        return $this->left_capacity;
    }

    public function setLeftCapacity(int $left_capacity): self
    {
        $this->left_capacity = $left_capacity;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(User $user): self
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @return Collection<int, Files>
     */
    public function getFiles(): Collection
    {
        return $this->files;
    }

    public function addFile(Files $file): self
    {
        if (!$this->files->contains($file)) {
            $this->files->add($file);
            $file->setStorage($this);
        }

        return $this;
    }

    public function removeFile(Files $file): self
    {
        if ($this->files->removeElement($file)) {
            // set the owning side to null (unless already changed)
            if ($file->getStorage() === $this) {
                $file->setStorage(null);
            }
        }

        return $this;
    }
}
