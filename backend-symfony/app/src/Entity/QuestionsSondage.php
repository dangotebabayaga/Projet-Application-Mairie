<?php
namespace App\Entity;
use App\Repository\QuestionsSondageRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: QuestionsSondageRepository::class)]
#[ORM\Table(name: 'questions_sondage')]
class QuestionsSondage
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(nullable: true)]
    private ?int $sondageId = null;

    #[ORM\Column(nullable: true)]
    private ?string $question = null;

}
