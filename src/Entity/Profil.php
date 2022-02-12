<?php

namespace App\Entity;

use App\Repository\ProfilRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: ProfilRepository::class)]
class Profil
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    #[Groups(["profil", "thread"])]
    private $id;

    #[ORM\Column(type: 'string', length: 50)]
    #[Groups(["profil", "thread"])]
    private $pseudo;

    #[ORM\Column(type: 'datetime')]
    #[Groups(["profil"])]
    private $dateInscription;

    #[ORM\OneToMany(mappedBy: 'profil', targetEntity: Post::class)]
    #[Orm\JoinColumn(onDelete: "CASCADE")]
    private $posts;

    #[ORM\ManyToMany(targetEntity: self::class, inversedBy: 'follower')]
    #[Groups(["profil"])]
    private $followed;

    #[ORM\ManyToMany(targetEntity: self::class, mappedBy: 'followed')]
    #[Groups(["profil"])]
    private $follower;

    public function __construct()
    {
        $this->posts = new ArrayCollection();
        $this->subscriber = new ArrayCollection();
        $this->profils = new ArrayCollection();
        $this->followed = new ArrayCollection();
        $this->follower = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getDateInscription(): ?\DateTimeInterface
    {
        return $this->dateInscription;
    }

    public function setDateInscription(\DateTimeInterface $dateInscription): self
    {
        $this->dateInscription = $dateInscription;

        return $this;
    }

    /**
     * @return Collection|Post[]
     */
    public function getPosts(): Collection
    {
        return $this->posts;
    }

    public function addPost(Post $post): self
    {
        if (!$this->posts->contains($post)) {
            $this->posts[] = $post;
            $post->setProfil($this);
        }

        return $this;
    }

    public function removePost(Post $post): self
    {
        if ($this->posts->removeElement($post)) {
            // set the owning side to null (unless already changed)
            if ($post->getProfil() === $this) {
                $post->setProfil(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|self[]
     */
    public function getFollowed(): Collection
    {
        return $this->followed;
    }

    public function addFollowed(self $followed): self
    {
        if (!$this->followed->contains($followed)) {
            $this->followed[] = $followed;
        }

        return $this;
    }

    public function removeFollowed(self $followed): self
    {
        $this->followed->removeElement($followed);

        return $this;
    }

    /**
     * @return Collection|self[]
     */
    public function getFollower(): Collection
    {
        return $this->follower;
    }

    public function addFollower(self $follower): self
    {
        if (!$this->follower->contains($follower)) {
            $this->follower[] = $follower;
            $follower->addFollowed($this);
        }

        return $this;
    }

    public function removeFollower(self $follower): self
    {
        if ($this->follower->removeElement($follower)) {
            $follower->removeFollowed($this);
        }

        return $this;
    }
}
