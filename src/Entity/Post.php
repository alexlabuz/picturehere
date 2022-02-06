<?php

namespace App\Entity;

use App\Repository\PostRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: PostRepository::class)]
class Post
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    #[Groups(["thread"])]
    private $id;

    #[ORM\Column(type: 'text')]
    #[Groups(["thread"])]
    private $message;

    #[ORM\Column(type: 'string', length: 255)]
    #[Groups(["thread"])]
    private $linkImage;

    #[ORM\Column(type: 'datetime')]
    #[Groups(["thread"])]
    private $date;

    #[ORM\ManyToOne(targetEntity: Profil::class, inversedBy: 'posts')]
    #[Orm\JoinColumn(onDelete: "CASCADE")]
    #[Groups(["thread"])]
    private $profil;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function setMessage(string $message): self
    {
        $this->message = $message;

        return $this;
    }

    public function getLinkImage(): ?string
    {
        return $this->linkImage;
    }

    public function setLinkImage(string $linkImage): self
    {
        $this->linkImage = $linkImage;

        return $this;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getProfil(): ?Profil
    {
        return $this->profil;
    }

    public function setProfil(?Profil $profil): self
    {
        $this->profil = $profil;

        return $this;
    }
}
