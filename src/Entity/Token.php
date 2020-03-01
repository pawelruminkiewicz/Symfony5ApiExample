<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\TokenRepository")
 */
class Token
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $tokenId;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $expired;

    public function __construct()
    {
        $this->tokenId = md5(date('H:i:s \O\n d/m/Y'));
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTokenId(): ?string
    {
        return $this->tokenId;
    }

    public function setTokenId(string $tokenId): self
    {
        $this->tokenId = $tokenId;

        return $this;
    }

    public function getExpired(): ?bool
    {
        return $this->expired;
    }

    public function setExpired(?bool $expired): self
    {
        $this->expired = $expired;

        return $this;
    }
}
