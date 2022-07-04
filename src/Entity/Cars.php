<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Cars
 *
 * @ORM\Table(name="cars")
 * @ORM\Entity
 */
class Cars
{
    /**
     * @var string
     *
     * @ORM\Column(name="license_plate", type="string", length=10, nullable=false)
     * @ORM\Id
     */
    private $licensePlate;

    /**
     * @var string
     *
     * @ORM\Column(name="plug_type", type="string", length=10, nullable=false)
     */
    private $plugType;

    public function getLicensePlate(): ?string
    {
        return $this->licensePlate;
    }

    public function getPlugType(): ?string
    {
        return $this->plugType;
    }

    public function setPlugType(string $plugType): self
    {
        $this->plugType = $plugType;

        return $this;
    }

    public function setLicensePlate(string $licensePlate): self
    {
        $this->licensePlate = $licensePlate;

        return $this;
    }

    public function __toString(): string
    {
        return $this->licensePlate;
    }
}
