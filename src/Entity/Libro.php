<?php

namespace App\Entity;

use App\Repository\LibroRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=LibroRepository::class)
 */
class Libro
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $titulo;

    /**
     * @ORM\Column(type="text")
     */
    private $sinopsis;

    /**
     * @ORM\Column(type="integer")
     * @Assert\Regex(
     * pattern="/^\d{4}$/",
     * message="El año de publicación debe tener 4 dígitos")    
     */
    private $anioPublicacion;

    /**
     * @ORM\Column(type="integer")
     * @Assert\Type(
     * type="integer",
     * message="La cantidad debe ser un número entero")
     */
    private $cantidad;

    /**
     * @ORM\ManyToOne(targetEntity=Autor::class, inversedBy="libros")
     */
    private $autores;

    /**
     * @ORM\ManyToMany(targetEntity=Categoria::class, mappedBy="libros")
     */
    private $categorias;

    /**
     * @ORM\OneToMany(targetEntity=Prestamo::class, mappedBy="libros")
     */
    private $prestamos;

    public function __construct()
    {
        $this->categorias = new ArrayCollection();
        $this->prestamos = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitulo(): ?string
    {
        return $this->titulo;
    }

    public function setTitulo(string $titulo): self
    {
        $this->titulo = $titulo;

        return $this;
    }

    public function getSinopsis(): ?string
    {
        return $this->sinopsis;
    }

    public function setSinopsis(string $sinopsis): self
    {
        $this->sinopsis = $sinopsis;

        return $this;
    }

    public function getAnioPublicacion(): ?int
    {
        return $this->anioPublicacion;
    }

    public function setAnioPublicacion(int $anioPublicacion): self
    {
        $this->anioPublicacion = $anioPublicacion;

        return $this;
    }

    public function getCantidad(): ?int
    {
        return $this->cantidad;
    }

    public function setCantidad(int $cantidad): self
    {
        $this->cantidad = $cantidad;

        return $this;
    }

    public function getAutores(): ?Autor
    {
        return $this->autores;
    }

    public function setAutores(?Autor $autores): self
    {
        $this->autores = $autores;

        return $this;
    }

    /**
     * @return Collection<int, Categoria>
     */
    public function getCategorias(): Collection
    {
        return $this->categorias;
    }

    public function addCategoria(Categoria $categoria): self
    {
        if (!$this->categorias->contains($categoria)) {
            $this->categorias[] = $categoria;
            $categoria->addLibro($this);
        }

        return $this;
    }

    public function removeCategoria(Categoria $categoria): self
    {
        if ($this->categorias->removeElement($categoria)) {
            $categoria->removeLibro($this);
        }

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
            $prestamo->setLibros($this);
        }

        return $this;
    }

    public function removePrestamo(Prestamo $prestamo): self
    {
        if ($this->prestamos->removeElement($prestamo)) {
            // set the owning side to null (unless already changed)
            if ($prestamo->getLibros() === $this) {
                $prestamo->setLibros(null);
            }
        }

        return $this;
    }
}
