<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Cars
 */
#[ORM\Table(name: "cars")]
#[ORM\Entity]
class Cars
{
    #[ORM\Column(name: "license_plate", type: "string", length: 10, nullable: false)]
    #[ORM\Id]
    private string $licensePlate;

     #[ORM\Column(name: "plug_type", type: "string", length: 10, nullable: false)]
    private string $plugType;

    #[ORM\ManyToMany(targetEntity: User::class, mappedBy: "cars")]
    private Collection $users;

    #[ORM\OneToOne(mappedBy: 'car', targetEntity: Bookings::class, cascade: ['persist', 'remove'])]
    private $booking;

    public function __construct()
    {
        $this->users = new ArrayCollection();
    }

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

    /**
     * @return Collection<int, User>
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function addUser(User $user): self
    {
        if (!$this->users->contains($user)) {
            $this->users[] = $user;
            $user->addCar($this);
        }

        return $this;
    }

    public function removeUser(User $user): self
    {
        if ($this->users->removeElement($user)) {
            $user->removeCar($this);
        }

        return $this;
    }

    public function getBooking(): ?Bookings
    {
        return $this->booking;
    }

    public function setBooking(Bookings $booking): self
    {
        // set the owning side of the relation if necessary
        if ($booking->getCar() !== $this) {
            $booking->setCar($this);
        }

        $this->booking = $booking;

        return $this;
    }
}
