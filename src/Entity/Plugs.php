<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Plugs
 *
 * @ORM\Table(name="plugs", indexes={@ORM\Index(name="Plugs_fk0", columns={"station"})})
 * @ORM\Entity
 */
class Plugs
{
    /**
     * @var int
     *
     * @ORM\Column(name="plug_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $plugId;

    /**
     * @var bool
     *
     * @ORM\Column(name="status", type="boolean", nullable=false)
     */
    private $status;

    /**
     * @var string
     *
     * @ORM\Column(name="connector_type", type="string", length=10, nullable=false)
     */
    private $connectorType;

    /**
     * @var string|null
     *
     * @ORM\Column(name="max_output", type="decimal", precision=5, scale=1, nullable=true, options={"default"="NULL"})
     */
    private $maxOutput = 'NULL';

    /**
     * @var \Stations
     *
     * @ORM\ManyToOne(targetEntity="Stations")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="station", referencedColumnName="plus_code")
     * })
     */
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

    public function getStation(): ?Stations
    {
        return $this->station;
    }

    public function setStation(?Stations $station): self
    {
        $this->station = $station;

        return $this;
    }

    public function __toString(): string
    {
        return 'id: ' . $this->plugId . '; station: ' . $this->station->getPlusCode() . '; connector: ' . $this->connectorType . '; max_output: ' . $this->maxOutput;
    }


}
