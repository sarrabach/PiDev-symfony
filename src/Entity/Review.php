<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

/**
 * Review
 *
 * @ORM\Table(name="review", uniqueConstraints={@ORM\UniqueConstraint(name="place_Id_3", columns={"place_Id"}), @ORM\UniqueConstraint(name="place_id_2", columns={"place_Id"})}, indexes={@ORM\Index(name="reviewPlace", columns={"id_User"}), @ORM\Index(name="place_Id", columns={"place_Id"}), @ORM\Index(name="place_Id_4", columns={"place_Id"})})
 * @ORM\Entity
 */
class Review
{
    /**
     * @var int
     *
     * @ORM\Column(name="Review_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $reviewId;

    /**
     * @var string
     *
     * @ORM\Column(name="Place_Name", type="string", length=30, nullable=false)
     */
    private $placeName;

    /**
     * @var int
     *
     * @ORM\Column(name="Rating", type="integer", nullable=false)
     */
    private $rating;

    /**
     * @var string|null
     *
     * @ORM\Column(name="Review_txt", type="string", length=120, nullable=true)
     */
    private $reviewTxt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="Review_date", type="date", nullable=false)
     */
    private $reviewDate;

    /**
     * @var \User
     *
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_User", referencedColumnName="id_User")
     * })
     */
    private $idUser;

    /**
     * @var \Placetovisit
     *
     * @ORM\ManyToOne(targetEntity="Placetovisit")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="place_Id", referencedColumnName="Place_Id")
     * })
     */
    private $place;

    public function getReviewId(): ?int
    {
        return $this->reviewId;
    }

    public function getPlaceName(): ?string
    {
        return $this->placeName;
    }

    public function setPlaceName(string $placeName): self
    {
        $this->placeName = $placeName;

        return $this;
    }

    public function getRating(): ?int
    {
        return $this->rating;
    }

    public function setRating(int $rating): self
    {
        $this->rating = $rating;

        return $this;
    }

    public function getReviewTxt(): ?string
    {
        return $this->reviewTxt;
    }

    public function setReviewTxt(?string $reviewTxt): self
    {
        $this->reviewTxt = $reviewTxt;

        return $this;
    }

    public function getReviewDate(): ?\DateTimeInterface
    {
        return $this->reviewDate;
    }

    public function setReviewDate(\DateTimeInterface $reviewDate): self
    {
        $this->reviewDate = $reviewDate;

        return $this;
    }

    public function getIdUser(): ?User
    {
        return $this->idUser;
    }

    public function setIdUser(?User $idUser): self
    {
        $this->idUser = $idUser;

        return $this;
    }

    public function getPlace(): ?Placetovisit
    {
        return $this->place;
    }

    public function setPlace(?Placetovisit $place): self
    {
        $this->place = $place;

        return $this;
    }


}
