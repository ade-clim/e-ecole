<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\MessagerieRepository")
 */
class Messagerie
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\MessageRecu", mappedBy="messagerie", cascade={"persist", "remove"})
     */
    private $messageRecu;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\MessageSend", mappedBy="messagerie", cascade={"persist", "remove"})
     */
    private $messageSend;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $email;

    public function __construct()
    {
        $this->messageRecu = new ArrayCollection();
        $this->messageSend = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Collection|MessageRecu[]
     */
    public function getMessageRecu(): Collection
    {
        return $this->messageRecu;
    }

    public function addMessageRecu(MessageRecu $messageRecu): self
    {
        if (!$this->messageRecu->contains($messageRecu)) {
            $this->messageRecu[] = $messageRecu;
            $messageRecu->setMessagerie($this);
        }

        return $this;
    }

    public function removeMessageRecu(MessageRecu $messageRecu): self
    {
        if ($this->messageRecu->contains($messageRecu)) {
            $this->messageRecu->removeElement($messageRecu);
            // set the owning side to null (unless already changed)
            if ($messageRecu->getMessagerie() === $this) {
                $messageRecu->setMessagerie(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|MessageSend[]
     */
    public function getMessageSend(): Collection
    {
        return $this->messageSend;
    }

    public function addMessageSend(MessageSend $messageSend): self
    {
        if (!$this->messageSend->contains($messageSend)) {
            $this->messageSend[] = $messageSend;
            $messageSend->setMessagerie($this);
        }

        return $this;
    }

    public function removeMessageSend(MessageSend $messageSend): self
    {
        if ($this->messageSend->contains($messageSend)) {
            $this->messageSend->removeElement($messageSend);
            // set the owning side to null (unless already changed)
            if ($messageSend->getMessagerie() === $this) {
                $messageSend->setMessagerie(null);
            }
        }

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }
}
