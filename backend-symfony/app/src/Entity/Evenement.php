<?php
namespace App\Entity;
use App\Repository\EvenementRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EvenementRepository::class)]
#[ORM\Table(name: 'Evenement')]
class Evenement
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(nullable: true)]
    private ?string $titre = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeInterface $dateev = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeInterface $heuredeb = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeInterface $heurefin = null;

    #[ORM\Column(nullable: true)]
    private ?int $administrateurId = null;

    #[ORM\Column(nullable: true)]
    private ?int $villeId = null;

    #[ORM\Column(nullable: true)]
    private ?int $type = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitre(): ?string
    {
        return $this->titre;
    }

    public function setTitre(?string $titre)
    {
        $this->titre = $titre;
    }

    public function getDateev(): ?\DateTimeInterface
    {
        return $this->dateev;
    }

    public function setDateev(?\DateTimeInterface $dateev)
    {
        $this->dateev = $dateev;
    }

    public function getHeuredeb(): ?\DateTimeInterface
    {
        return $this->heuredeb;
    }

    public function setHeuredeb(?\DateTimeInterface $heuredeb)
    {
        $this->heuredeb = $heuredeb;
    }

    public function getHeurefin(): ?\DateTimeInterface
    {
        return $this->heurefin;
    }

    public function setHeurefin(?\DateTimeInterface $heurefin)
    {
        $this->heurefin = $heurefin;
    }

    public function getAdministrateurId(): ?int
    {
        return $this->administrateurId;
    }

    public function setAdministrateurId(?int $administrateurId)
    {
        $this->administrateurId = $administrateurId;
    }

    public function getVilleId(): ?int
    {
        return $this->villeId;
    }

    public function setVilleId(?int $villeId)
    {
        $this->villeId = $villeId;
    }

    public function getType(): ?int
    {
        return $this->type;
    }

    public function setType(?int $type)
    {
        $this->type = $type;
    }


}
