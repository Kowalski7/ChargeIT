<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Stations
 *
 * @ORM\Table(name="stations")
 * @ORM\Entity
 */
class Stations
{
    /**
     * @ORM\Id
     * @ORM\Column(type="guid")
     */
    private $uuid;

    /**
     * @var string
     *
     * @ORM\Column(name="plus_code", type="string", length=30, nullable=false)
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $plusCode;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=50, nullable=false)
     */
    private $name;

    public function getPlusCode(): ?string
    {
        return $this->plusCode;
    }

    public function setPlusCode(string $plusCode): void
    {
        $this->plusCode = $plusCode;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getUuid(): ?string
    {
        return $this->uuid;
    }

    public function setUuid(string $uuid): self
    {
        $this->uuid = $uuid;

        return $this;
    }


}
