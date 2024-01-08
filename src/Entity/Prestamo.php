<?php

namespace App\Entity;

use App\Repository\PrestamoRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=PrestamoRepository::class)
 */
class Prestamo
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="date")
     */
    private $fechaInicio;

    /**
     * @ORM\Column(type="date")
     */
    private $fechaDevolucion;

    /**
     * @ORM\ManyToOne(targetEntity=Libro::class, inversedBy="prestamos")
     */
    private $libros;

    /**
     * @ORM\ManyToOne(targetEntity=Usuario::class, inversedBy="prestamos")
     */
    private $usuarios;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFechaInicio(): ?\DateTimeInterface
    {
        return $this->fechaInicio;
    }

    public function getFechaInicioFormateada(): string
    {
        return $this->fechaInicio->format('d-m-Y');
    }

    public function setFechaInicio(\DateTimeInterface $fechaInicio): self
    {
        $this->fechaInicio = $fechaInicio;

        return $this;
    }

    public function getFechaDevolucion(): ?\DateTimeInterface
    {
        return $this->fechaDevolucion;
    }

    public function getFechaDevolucionFormateada(): string
    {
        return $this->fechaDevolucion->format('d-m-Y');
    }

    public function setFechaDevolucion(\DateTimeInterface $fechaDevolucion): self
    {
        $this->fechaDevolucion = $fechaDevolucion;

        return $this;
    }

    public function getLibros(): ?Libro
    {
        return $this->libros;
    }

    public function setLibros(?Libro $libros): self
    {
        $this->libros = $libros;

        return $this;
    }

    public function getUsuarios(): ?Usuario
    {
        return $this->usuarios;
    }

    public function setUsuarios(?Usuario $usuarios): self
    {
        $this->usuarios = $usuarios;

        return $this;
    }
}
