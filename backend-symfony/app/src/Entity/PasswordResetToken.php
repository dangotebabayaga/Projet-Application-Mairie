<?php
namespace App\Entity;

use App\Repository\PasswordResetTokenRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PasswordResetTokenRepository::class)]
#[ORM\Table(name: 'password_reset_tokens')]
class PasswordResetToken
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(name: 'user_id', type: 'integer')]
    private ?int $userId = null;

    #[ORM\Column(length: 64, unique: true)]
    private ?string $token = null;

    #[ORM\Column(name: 'expires_at', type: 'datetime')]
    private ?\DateTimeInterface $expiresAt = null;

    #[ORM\Column(type: 'boolean', options: ['default' => false])]
    private bool $used = false;

    public function getId(): ?int { return $this->id; }
    public function getUserId(): ?int { return $this->userId; }
    public function setUserId(int $userId): self { $this->userId = $userId; return $this; }
    public function getToken(): ?string { return $this->token; }
    public function setToken(string $token): self { $this->token = $token; return $this; }
    public function getExpiresAt(): ?\DateTimeInterface { return $this->expiresAt; }
    public function setExpiresAt(\DateTimeInterface $expiresAt): self { $this->expiresAt = $expiresAt; return $this; }
    public function isUsed(): bool { return $this->used; }
    public function setUsed(bool $used): self { $this->used = $used; return $this; }

    public function isValid(): bool
    {
        return !$this->used && $this->expiresAt > new \DateTime();
    }
}
