<?php

namespace App\Entity;

use App\Repository\ClassroomRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\BlobType;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use phpDocumentor\Reflection\PseudoTypes\True_;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;

#[ORM\Entity(repositoryClass: ClassroomRepository::class)]
class Classroom
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $subject = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: 'teacher_id')]
    private ?User $teacher = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $meetingTimes = null;

    #[ORM\Column(type: Types::INTEGER)]
    private ?string $currentCount = null;

    #[ORM\Column(type: Types::INTEGER)]
    private ?string $maxCount = null;

    #[ORM\ManyToMany(targetEntity: User::class, mappedBy: 'classes')]
    private Collection $user;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $syllabus = null;

    public function __construct()
    {
        $this->getStudent = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSubject(): ?string
    {
        return $this->subject;
    }

    public function setSubject($subject)
    {
        $this->subject = $subject;
    }

    public function getTeacher(): ?User
    {
        return $this->teacher;
    }

    public function setTeacher($teacher)
    {
        $this->teacher = $teacher;
    }

    public function getCurrentCount()
    {
        return $this->currentCount;
    }

    public function setCurrentCount($currentCount)
    {
        $this->currentCount = $currentCount;
    }

    public function getMaxCount()
    {
        return $this->maxCount;
    }

    public function setMaxCount($maxCount)
    {
        $this->maxCount = $maxCount;
    }

    public function getMeetingTimes()
    {
        return $this->meetingTimes;
    }

    public function setMeetingTimes($meetingTimes)
    {
        $this->meetingTimes = $meetingTimes;
    }

    public function getUser(): Collection
    {
        return $this->user;
    }

    public function addUser(User $user): self
    {
        if (!$this->user->contains($user)) {
            $this->user->add($user);
            $user->addClass($this);
        }

        return $this;
    }

    public function removeUser(User $user): self
    {
        if ($this->user->removeElement($user)) {
            $user->removeClass($this);
        }

        return $this;
    }

    public function getSyllabus(): ?string
    {
        return $this->syllabus;
    }

    public function setSyllabus(?string $syllabus): self
    {
        $this->syllabus = $syllabus;

        return $this;
    }
}
