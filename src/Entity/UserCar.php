<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * UserCar
 *
 * @ORM\Table(name="user_car", indexes={@ORM\Index(name="User_Car_fk0", columns={"user"}), @ORM\Index(name="User_Car_fk1", columns={"car"})})
 * @ORM\Entity
 */
class UserCar
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var \Users
     *
     * @ORM\ManyToOne(targetEntity="Users")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="user", referencedColumnName="email")
     * })
     */
    private $user;

    /**
     * @var \Cars
     *
     * @ORM\ManyToOne(targetEntity="Cars")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="car", referencedColumnName="license_plate")
     * })
     */
    private $car;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?Users
    {
        return $this->user;
    }

    public function setUser(?Users $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getCar(): ?Cars
    {
        return $this->car;
    }

    public function setCar(?Cars $car): self
    {
        $this->car = $car;

        return $this;
    }


}