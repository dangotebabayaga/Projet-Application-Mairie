<?php
namespace App\Entity;
use App\Repository\SondagesRepository;
use Doctrine\ORM\Mapping as ORM;
use phpDocumentor\Reflection\DocBlock\Description;

#[ORM\Entity(repositoryClass: SondagesRepository::class)]
#[ORM\Table(name: 'sondages')]
class Sondages
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $titre = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $description = null;

    #[ORM\Column(name: 'date_debut', type: 'datetime', nullable: true)]
    private ?\DateTimeInterface $dateDebut = null;

    #[ORM\Column(name: 'date_fin', type: 'datetime', nullable: true)]
    private ?\DateTimeInterface $dateFin = null;

    #[ORM\Column(name: 'administrateur_id', nullable: true)]
    private ?int $administrateurId = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitre(): ?string
    {
        return $this->titre;
    }

    public function setTitre(?string $titre){
        $this->titre = $titre;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description){
        $this->description=$description;
    }

    public function getDateDebut(): ?\DateTimeInterface
    {
        return $this->dateDebut;
    }

    public function setDateDebut(?\DateTimeInterface $dateDebut){
        $this->dateDebut = $dateDebut;
    }

    public function getDateFin(): ?\DateTimeInterface
    {
        return $this->dateFin;
    }
    public function setDateFin(?\DateTimeInterface $dateFin){
        $this->dateFin=$dateFin;
    }
    public function getAdministrateur(): ?int
    {
        return $this->administrateurId;
    }
    public function setAdminstrateur(int $idAdmin){
        $this->administrateurId=$idAdmin;
    }

}
