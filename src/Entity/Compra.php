<?php

namespace App\Entity;

use App\Repository\CompraRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CompraRepository::class)]
#[ORM\Table(name: 'compras')]
class Compra
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Usuario::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?Usuario $usuario = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $fechaCompra = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $total = null;

    #[ORM\Column(length: 20)]
    private ?string $estado = 'completada';

    #[ORM\OneToMany(mappedBy: 'compra', targetEntity: CompraProducto::class, cascade: ['persist', 'remove'])]
    private Collection $items;

    public function __construct()
    {
        $this->items = new ArrayCollection();
        $this->fechaCompra = new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsuario(): ?Usuario
    {
        return $this->usuario;
    }

    public function setUsuario(?Usuario $usuario): static
    {
        $this->usuario = $usuario;
        return $this;
    }

    public function getFechaCompra(): ?\DateTimeInterface
    {
        return $this->fechaCompra;
    }

    public function setFechaCompra(\DateTimeInterface $fechaCompra): static
    {
        $this->fechaCompra = $fechaCompra;
        return $this;
    }

    public function getTotal(): ?string
    {
        return $this->total;
    }

    public function setTotal(string $total): static
    {
        $this->total = $total;
        return $this;
    }

    public function getEstado(): ?string
    {
        return $this->estado;
    }

    public function setEstado(string $estado): static
    {
        $this->estado = $estado;
        return $this;
    }

    /**
     * @return Collection<int, CompraProducto>
     */
    public function getItems(): Collection
    {
        return $this->items;
    }

    public function addItem(CompraProducto $item): static
    {
        if (!$this->items->contains($item)) {
            $this->items->add($item);
            $item->setCompra($this);
        }
        return $this;
    }

    public function removeItem(CompraProducto $item): static
    {
        if ($this->items->removeElement($item)) {
            if ($item->getCompra() === $this) {
                $item->setCompra(null);
            }
        }
        return $this;
    }
}
