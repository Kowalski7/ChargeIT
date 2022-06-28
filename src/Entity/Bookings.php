<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Bookings
 *
 * @ORM\Table(name="bookings", indexes={@ORM\Index(name="Bookings_fk0", columns={"car"}), @ORM\Index(name="Bookings_fk1", columns={"plug"})})
 * @ORM\Entity
 */
class Bookings
{
    /**
     * @var int
     *
     * @ORM\Column(name="booking_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $bookingId;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="start_time", type="datetime", nullable=false)
     */
    private $startTime;

    /**
     * @var int
     *
     * @ORM\Column(name="duration", type="integer", nullable=false)
     */
    private $duration;

    /**
     * @var \Cars
     *
     * @ORM\ManyToOne(targetEntity="Cars")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="car", referencedColumnName="license_plate")
     * })
     */
    private $car;

    /**
     * @var \Plugs
     *
     * @ORM\ManyToOne(targetEntity="Plugs")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="plug", referencedColumnName="plug_id")
     * })
     */
    private $plug;

    public function getBookingId(): ?int
    {
        return $this->bookingId;
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

    public function getCar(): ?Cars
    {
        return $this->car;
    }

    public function setCar(?Cars $car): self
    {
        $this->car = $car;

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


}
