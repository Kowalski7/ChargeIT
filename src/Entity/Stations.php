<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Stations
 */
#[ORM\Table(name: "stations")]
#[ORM\Entity]
class Stations
{
    #[ORM\Id]
    #[ORM\Column(name: "uuid", type: "guid", length: 36, unique: true, nullable: false)]
    private string $uuid;

    #[ORM\Column(name: "plus_code", type: "string", length: 30, nullable: false)]
    private string $plusCode;

    /**
     * @var string
     */
     #[ORM\Column(name: "name", type: "string", length: 50, nullable: false)]
    private string $name;

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

    public function __toString(): string
    {
        return '{uuid: ' . $this->uuid . ', name: ' . $this->name . ', plusCode: ' . $this->plusCode . '}';
    }


}
