<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: "plugs")]
#[ORM\Index(columns: ["station"], name: "Plugs_fk0")]
#[ORM\Entity]
class Plugs
{
    #[ORM\Column(name: "plug_id", type: "integer", nullable: false)]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: "IDENTITY")]
    private int $plugId;

    #[ORM\Column(name: "status", type: "boolean", nullable: false)]
    private bool $status;

    #[ORM\Column(name: "connector_type", type: "string", length: 10, nullable: false)]
    private string $connectorType;

    #[ORM\Column(name: "max_output", type: "decimal", precision: 5, scale: 1, nullable: true, options: ["default" => NULL])]
    private string|null $maxOutput = NULL;

    #[ORM\ManyToOne(targetEntity: Stations::class, inversedBy: 'plugs')]
    #[ORM\JoinColumn(name: "station", referencedColumnName: "uuid", nullable: false)]
    private $station;

    public function getPlugId(): ?int
    {
        return $this->plugId;
    }

    public function getStatus(): ?bool
    {
        return $this->status;
    }

    public function setStatus(bool $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getConnectorType(): ?string
    {
        return $this->connectorType;
    }

    public function setConnectorType(string $connectorType): self
    {
        $this->connectorType = $connectorType;

        return $this;
    }

    public function getMaxOutput(): ?string
    {
        return $this->maxOutput;
    }

    public function setMaxOutput(?string $maxOutput): self
    {
        $this->maxOutput = $maxOutput;

        return $this;
    }

    public function __toString(): string
    {
        return '{id: ' . $this->plugId . ', station: ' . $this->station->getUuid() . ', connector: ' . $this->connectorType . ', max_output: ' . $this->maxOutput . '}';
    }

    public function getStation(): ?Stations
    {
        return $this->station;
    }

    public function setStation(?Stations $station): self
    {
        $this->station = $station;

        return $this;
    }
}
