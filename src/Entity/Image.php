<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ImageRepository")
 * @Vich\Uploadable
 */
class Image
{
    public const NUM_ITEMS = 20;
    public const MAX_RESULT = 100;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * NOTE: This is not a mapped field of entity metadata, just a simple property.
     *
     * @Vich\UploadableField(mapping="image_images", fileNameProperty="imageName", size="imageSize")
     *
     * @var File|null
     * @Assert\File(maxSize="3M",
     *     maxSizeMessage="Il file è troppo grande ({{ size }} {{ suffix }}). Massimo consentito: {{ limit }} {{ suffix }}.",
     *     mimeTypes={"image/jpeg"},
     *     mimeTypesMessage="Il tipo di file non è corretto ({{ type }}). Puoi caricare solamente immagini {{ types }}."
     * )
     */
    private $imageFile;

    /**
     * @ORM\Column(type="datetime")
     */
    private $uploadAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $viewAt;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $views;

    /**
     * @ORM\Column(type="string", length=255)
     * @var string|null
     */
    private $imageName;

    /**
     * @ORM\Column(type="integer")
     * @var int|null
     */
    private $imageSize;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Category", inversedBy="images")
     * @ORM\JoinColumn(nullable=false)
     */
    private $category;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Tag", cascade={"persist"}, inversedBy="images")
     * @ORM\OrderBy({"name": "ASC"})
     * @Assert\Count(max="3", maxMessage="Massimo 3 Tags!")
     */
    private $tags;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $description;

    public function __toString()
    {
        return $this->imageName;
    }


    public function __construct()
    {
        $this->tags = new ArrayCollection();
        $this->uploadAt = new \DateTime();
        $this->views = 0;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * If manually uploading a file (i.e. not using Symfony Form) ensure an instance
     * of 'UploadedFile' is injected into this setter to trigger the update. If this
     * bundle's configuration parameter 'inject_on_load' is set to 'true' this setter
     * must be able to accept an instance of 'File' as the bundle will inject one here
     * during Doctrine hydration.
     *
     * @param File|\Symfony\Component\HttpFoundation\File\UploadedFile|null $imageFile
     */
    public function setImageFile(?File $imageFile = null): void
    {
        $this->imageFile = $imageFile;

        if (null !== $imageFile) {
            // It is required that at least one field changes if you are using doctrine
            // otherwise the event listeners won't be called and the file is lost
            $this->uploadAt = new \DateTimeImmutable();
        }
    }

    public function getImageFile(): ?File
    {
        return $this->imageFile;
    }


    public function getUploadAt(): ?\DateTimeInterface
    {
        return $this->uploadAt;
    }

    public function setUploadAt(\DateTimeInterface $uploadAt): self
    {
        $this->uploadAt = $uploadAt;

        return $this;
    }

    public function getViewAt(): ?\DateTimeInterface
    {
        return $this->viewAt;
    }

    public function setViewAt(?\DateTimeInterface $viewAt): self
    {
        $this->viewAt = $viewAt;

        return $this;
    }

    public function getViews(): ?int
    {
        return $this->views;
    }

    public function setViews(?int $views): self
    {
        $this->views = $views;

        return $this;
    }

    public function getImageName(): ?string
    {
        return $this->imageName;
    }

    public function setImageName(?string $imageName): self
    {
        $this->imageName = $imageName;

        return $this;
    }

    public function getImageSize(): ?int
    {
        return $this->imageSize;
    }

    public function setImageSize(?int $imageSize): self
    {
        $this->imageSize = $imageSize;

        return $this;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): self
    {
        $this->category = $category;

        return $this;
    }

    /**
     * @return Collection|Tag[]
     */
    public function getTags(): Collection
    {
        return $this->tags;
    }

    public function addTag(Tag $tag): self
    {
        if (!$this->tags->contains($tag)) {
            $this->tags[] = $tag;
        }

        return $this;
    }

    public function removeTag(Tag $tag): self
    {
        if ($this->tags->contains($tag)) {
            $this->tags->removeElement($tag);
        }

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }
}
