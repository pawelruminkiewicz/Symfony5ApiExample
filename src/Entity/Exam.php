<?php

namespace App\Entity;

use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Swagger\Annotations as SWG;
use JMS\Serializer\Annotation as JMS;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ExamRepository")
 */
class Exam
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @JMS\Exclude()
     */
    private $id;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @SWG\Property(example="10")
     */
    private $points;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     * @SWG\Property(example="Mr. John Doe")
     */
    private $teacher;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @SWG\Property(example="Example exam description")
     */
    private $description;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPoints(): ?int
    {
        return $this->points;
    }

    public function setPoints(?int $points): self
    {
        $this->points = $points;

        return $this;
    }

    public function getTeacher(): ?string
    {
        return $this->teacher;
    }

    public function setTeacher(?string $teacher): self
    {
        $this->teacher = $teacher;

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
    /**
     * @JMS\Exclude()
    */
    public function getMD5Shortcut(): string
    {
        return md5(
            $this->getPoints().
            $this->getTeacher().
            $this->getDescription()
        );
    }
}
