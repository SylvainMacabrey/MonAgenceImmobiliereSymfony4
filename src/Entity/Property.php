<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Cocur\Slugify\Slugify;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Vich\UploaderBundle\Entity\File as EmbeddedFile;

/**
 * @ORM\Entity(repositoryClass="App\Repository\PropertyRepository")
 * @UniqueEntity("title")
 * @Vich\Uploadable
 */
class Property
{
    const HEAT = [
        0 => 'Electrique',
        1 => 'Gaz',
        2 => 'Bois'
    ];

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    //Assert\Image(mimeTypes="image\jpeg") => bloque jpg
    /**
     * @Vich\UploadableField(mapping="property_image", fileNameProperty="filename")
     * @var File|null
     */
    private $imageFile;

    /**
     * @ORM\Column(type="string", length=255)
     * @var string
     */
    private $filename;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $title;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @Assert\Length(min=5, max=255)
     */
    private $description;

    /**
     * @ORM\Column(type="integer")
     * @Assert\Range(min=10, max=400)
     */
    private $surface;

    /**
     * @ORM\Column(type="integer")
     */
    private $rooms;

    /**
     * @ORM\Column(type="integer")
     */
    private $bedroom;

    /**
     * @ORM\Column(type="integer")
     */
    private $floor;

    /**
     * @ORM\Column(type="integer")
     */
    private $price;

    /**
     * @ORM\Column(type="integer")
     */
    private $heat;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $city;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $address;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Regex("/^[0-9]{5}$/")
     */
    private $postalCode;

    /**
     * @ORM\Column(type="boolean", options={"default": false})
     */
    private $sold = false;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="datetime")
     */
    private $updatedAt;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Option", inversedBy="properties")
     */
    private $options;

    public function __construct() {
        $this->createdAt = new \DateTime();
        $this->options = new ArrayCollection();
    }

    public function getId(): ?int {
        return $this->id;
    }

    public function getImageFile(): ?File {
        return $this->imageFile;
    }

    public function setImageFile(File $imageFile): self {
        $this->imageFile = $imageFile;
        if ($this->imageFile instanceof UploadedFile) {
            $this->updatedAt = new \DateTime('now');
        }
        return $this;
    }

    public function getFilename(): ?string {
        return $this->filename;
    }

    public function setFilename(string $filename): self {
        $this->filename = $filename;
        return $this;
    }

    public function getTitle(): ?string {
        return $this->title;
    }

    public function setTitle(string $title): self {
        $this->title = $title;
        return $this;
    }

    public function getSlug(): ?string {
        return (new Slugify())->slugify($this->title);
    }

    public function getDescription(): ?string {
        return $this->description;
    }

    public function setDescription(?string $description): self {
        $this->description = $description;
        return $this;
    }

    public function getSurface(): ?int {
        return $this->surface;
    }

    public function setSurface(int $surface): self {
        $this->surface = $surface;
        return $this;
    }

    public function getRooms(): ?int {
        return $this->rooms;
    }

    public function setRooms(int $rooms): self {
        $this->rooms = $rooms;
        return $this;
    }

    public function getBedroom(): ?int {
        return $this->bedroom;
    }

    public function setBedroom(int $bedroom): self {
        $this->bedroom = $bedroom;
        return $this;
    }

    public function getFloor(): ?int {
        return $this->floor;
    }

    public function setFloor(int $floor): self {
        $this->floor = $floor;
        return $this;
    }

    public function getPrice(): ?int {
        return $this->price;
    }

    public function getFormattedPrice(): ?string {
        return number_format($this->price, 0, '', ' ');
    }

    public function setPrice(int $price): self {
        $this->price = $price;
        return $this;
    }

    public function getHeat(): ?int {
        return $this->heat;
    }

    public function getHeatType(): ?string {
        return self::HEAT[$this->heat];
    }

    public function setHeat(int $heat): self {
        $this->heat = $heat;
        return $this;
    }

    public function getCity(): ?string {
        return $this->city;
    }

    public function setCity(string $city): self {
        $this->city = $city;
        return $this;
    }

    public function getAddress(): ?string {
        return $this->address;
    }

    public function setAddress(string $address): self {
        $this->address = $address;
        return $this;
    }

    public function getPostalCode(): ?string {
        return $this->postalCode;
    }

    public function setPostalCode(string $postalCode): self {
        $this->postalCode = $postalCode;
        return $this;
    }

    public function getSold(): ?bool {
        return $this->sold;
    }

    public function setSold(bool $sold): self {
        $this->sold = $sold;
        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self {
        $this->createdAt = $createdAt;
        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeInterface $updatedAt): self {
        $this->updatedAt = $updatedAt;
        return $this;
    }

    /**
     * @return Collection|Option[]
     */
    public function getOptions(): Collection {
        return $this->options;
    }

    public function addOption(Option $option): self {
        if (!$this->options->contains($option)) {
            $this->options[] = $option;
            $option->addProperty($this);
        }
        return $this;
    }

    public function removeOption(Option $option): self {
        if ($this->options->contains($option)) {
            $this->options->removeElement($option);
            $option->removeProperty($this);
        }
        return $this;
    }

}
