<?php

namespace App\Entity;

use App\Repository\UsuarioRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=UsuarioRepository::class)
 */
class Usuario
{
    /**
     *  Representa el identificador unico del contrato-PK. Se genera automaticamente
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $nombre;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Email(message = "El correo electrónico '{{ value }}' no es válido.")
     */
    private $correoElectronico;

    /**
     * @ORM\OneToMany(targetEntity=Prestamo::class, mappedBy="usuarios")
     */
    private $prestamos;

    public function __construct()
    {
        $this->prestamos = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNombre(): ?string
    {
        return $this->nombre;
    }

    public function setNombre(string $nombre): self
    {
        $this->nombre = $nombre;

        return $this;
    }

    public function getCorreoElectronico(): ?string
    {
        return $this->correoElectronico;
    }

    public function setCorreoElectronico(string $correoElectronico): self
    {
        $this->correoElectronico = $correoElectronico;

        return $this;
    }

    /**
     * @return Collection<int, Prestamo>
     */
    public function getPrestamos(): Collection
    {
        return $this->prestamos;
    }

    public function addPrestamo(Prestamo $prestamo): self
    {
        if (!$this->prestamos->contains($prestamo)) {
            $this->prestamos[] = $prestamo;
            $prestamo->setUsuarios($this);
        }

        return $this;
    }

    public function removePrestamo(Prestamo $prestamo): self
    {
        if ($this->prestamos->removeElement($prestamo)) {
            // set the owning side to null (unless already changed)
            if ($prestamo->getUsuarios() === $this) {
                $prestamo->setUsuarios(null);
            }
        }

        return $this;
    }
}
