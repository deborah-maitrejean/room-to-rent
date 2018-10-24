<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\BookingRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Booking
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="bookings")
     * @ORM\JoinColumn(nullable=false)
     */
    private $booker;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Ad", inversedBy="bookings")
     * @ORM\JoinColumn(nullable=false)
     */
    private $ad;

    /**
     * @ORM\Column(type="datetime")
     * @Assert\DateTime(message="Attention, la date doit être au bon format.")
     * @Assert\GreaterThan("today", message="La date d'arrivée doit être inférieure à la date d'aujourd'hui.")
     */
    private $startDate;

    /**
     * @ORM\Column(type="datetime")
     * @Assert\DateTime(message="Attention, la date doit être au bon format.")
     * @Assert\GreaterThan(propertyPath="startDate", message="La date de départ doit être plus éloignée que la date d'arrivée.")
     */
    private $endDate;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="float")
     */
    private $amount;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $comment;

    /**
     * Callback appelé à chaque fois qu'on crée une réservation
     * @ORM\PrePersist()
     */
    public function prePersist()
    {
        if (empty($this->createdAt)) {
            $this->createdAt = new \DateTime();
        }
        if (empty($this->amount)) {
            // multiplier le prix de l'annonce par le nombre de jours où va rester le booker
            $this->amount = $this->ad->getPrice() * $this->getDuration();
        }
    }

    public function getDuration()
    {
        $difference = $this->endDate->diff($this->startDate);
        // $difference est de type DateInterval

        return $difference->days;
    }

    public function isBookableDate()
    {
        // 1) Il faut connaître les dates qui sont impossibles pour l'annonce
        // ensemble des jours pour lequels l'annonce n'est pas disponible
        $notAvailableDays = $this->ad->getNotAvailableDays();
        // 2) Il faut comparer les dates choisies avec les dates impossibles
        $bookingDays = $this->getDays(); //  ensemble des journées (objets DateTime) de la réservation que je suis en train de passer

        // sert à convertir les objets DateTime en string
        $formatDay = function ($day) {
          return $day->format('Y-m-d');
        };

        // on convertit les objets DateTime de la réservation en string
        // tableau des chaines de caraactères des journées réservées
        $days = array_map($formatDay, $bookingDays);

        // on convertit les objets DateTime en string pour les jours qui ne sont pas disponibles
        $notAvailable = array_map($formatDay, $notAvailableDays);

        foreach ($days as $day) { // on boucle sur les journées qui concernent ma réservation
            // est-ce que dans le tableau des jours non disponibles, ma journée s'y trouve
            if (array_search($day, $notAvailable) !== false) {
                // notre journée n'est pas disponible
                return false;
            }
        }

        // si notre journée est disponible
        return true;
    }

    /**
     * Permet de récupérer un tableau des journées qui correspondent à ma réservation
     * @return array Un tableau d'objets DateTime représentant les jours de la réservation
     */
    public function getDays()
    {
        $result = range(
            $this->startDate->getTimestamp(),
            $this->endDate->getTimestamp(),
            24 * 60 * 60
        );

        // jours qui correspondent à la réservation
        $days = array_map(function ($dayTimestamp){
           return new \DateTime(date('Y-m-d', $dayTimestamp));
        }, $result);

        return $days;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getBooker(): ?User
    {
        return $this->booker;
    }

    public function setBooker(?User $booker): self
    {
        $this->booker = $booker;

        return $this;
    }

    public function getAd(): ?Ad
    {
        return $this->ad;
    }

    public function setAd(?Ad $ad): self
    {
        $this->ad = $ad;

        return $this;
    }

    public function getStartDate(): ?\DateTimeInterface
    {
        return $this->startDate;
    }

    public function setStartDate(\DateTimeInterface $startDate): self
    {
        $this->startDate = $startDate;

        return $this;
    }

    public function getEndDate(): ?\DateTimeInterface
    {
        return $this->endDate;
    }

    public function setEndDate(\DateTimeInterface $endDate): self
    {
        $this->endDate = $endDate;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getAmount(): ?float
    {
        return $this->amount;
    }

    public function setAmount(float $amount): self
    {
        $this->amount = $amount;

        return $this;
    }

    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function setComment(?string $comment): self
    {
        $this->comment = $comment;

        return $this;
    }
}
