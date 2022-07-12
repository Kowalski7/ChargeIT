<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
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

    #[ORM\Column(name: "lat", type: "decimal", precision: 18, scale: 15, nullable: false)]
    private string $lat;

    #[ORM\Column(name: "lon", type: "decimal", precision: 18, scale: 15, nullable: false)]
    private string $lon;

    /**
     * @var string
     */
     #[ORM\Column(name: "name", type: "string", length: 50, nullable: false)]
    private string $name;

     #[ORM\OneToMany(mappedBy: 'station', targetEntity: Plugs::class, orphanRemoval: true)]
     private $plugs;

     public function __construct()
     {
         $this->plugs = new ArrayCollection();
     }

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

    /**
     * @return string
     */
    public function getLat(): string
    {
        return $this->lat;
    }

    /**
     * @param string $lat
     */
    public function setLat(string $lat): void
    {
        $this->lat = $lat;
    }

    /**
     * @return string
     */
    public function getLon(): string
    {
        return $this->lon;
    }

    /**
     * @param string $lon
     */
    public function setLon(string $lon): void
    {
        $this->lon = $lon;
    }

    public function __toString(): string
    {
        return '{uuid: ' . $this->uuid . ', name: ' . $this->name . ', plusCode: ' . $this->plusCode . '}';
    }

    /**
     * @return Collection<int, Plugs>
     */
    public function getPlugs(): Collection
    {
        return $this->plugs;
    }

    public function addPlug(Plugs $plug): self
    {
        if (!$this->plugs->contains($plug)) {
            $this->plugs[] = $plug;
            $plug->setStation($this);
        }

        return $this;
    }

    public function removePlug(Plugs $plug): self
    {
        if ($this->plugs->removeElement($plug)) {
            // set the owning side to null (unless already changed)
            if ($plug->getStation() === $this) {
                $plug->setStation(null);
            }
        }

        return $this;
    }


}
