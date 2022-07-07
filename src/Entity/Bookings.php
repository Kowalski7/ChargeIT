<?php

namespace App\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * Bookings
 */
#[ORM\Table(name: "bookings")]
#[ORM\Index(columns: ["car"], name: "Bookings_fk0")]
#[ORM\Index(columns: ["plug"], name: "Bookings_fk1")]
#[ORM\Entity]
class Bookings
{
    #[ORM\Column(name: "id", type: "integer", nullable: false)]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: "IDENTITY")]
    private int $id;

    #[ORM\Column(name: "start_time", type: "datetime", nullable: false)]
    private mixed $startTime;

    #[ORM\Column(name: "duration", type: "integer", nullable: false)]
    private int $duration;

//    #[ORM\ManyToOne(targetEntity: "Cars")]
//    #[ORM\JoinColumn(name: "car", referencedColumnName: "license_plate", nullable: false)]
//    private Cars $car;

    #[ORM\ManyToOne(targetEntity: "Plugs")]
    #[ORM\JoinColumn(name: "plug", referencedColumnName: "plug_id", nullable: false)]
    private Plugs $plug;

    #[ORM\Column(type: "datetime")]
    private mixed $end_time;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'bookings')]
    #[ORM\JoinColumn(nullable: false)]
    private User $user;

    #[ORM\OneToOne(inversedBy: 'booking', targetEntity: Cars::class)]
    #[ORM\JoinColumn(name: 'car', referencedColumnName: 'license_plate', nullable: false)]
    private $car;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStartTime(): ?\DateTimeInterface
    {
        return $this->startTime;
    }

    public function setStartTime(\DateTimeInterface $startTime): self
    {
        $this->startTime = $startTime;

        return $this;
    }

    public function getDuration(): ?int
    {
        return $this->duration;
    }

    public function setDuration(int $duration): self
    {
        $this->duration = $duration;

        return $this;
    }

    public function getPlug(): ?Plugs
    {
        return $this->plug;
    }

    public function setPlug(?Plugs $plug): self
    {
        $this->plug = $plug;

        return $this;
    }

    public function getEndTime(): ?\DateTimeInterface
    {
        return $this->end_time;
    }

    public function setEndTime(\DateTimeInterface $end_time): self
    {
        $this->end_time = $end_time;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getCar(): ?Cars
    {
        return $this->car;
    }

    public function setCar(Cars $car): self
    {
        $this->car = $car;

        return $this;
    }
}
