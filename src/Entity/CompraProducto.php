<?php

namespace App\Entity;

use App\Repository\CompraProductoRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CompraProductoRepository::class)]
#[ORM\Table(name: 'compra_productos')]
class CompraProducto
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Compra::class, inversedBy: 'items')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Compra $compra = null;

    #[ORM\ManyToOne(targetEntity: Producto::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?Producto $producto = null;

    #[ORM\Column]
    private ?int $cantidad = 1;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $precioUnitario = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCompra(): ?Compra
    {
        return $this->compra;
    }

    public function setCompra(?Compra $compra): static
    {
        $this->compra = $compra;
        return $this;
    }

    public function getProducto(): ?Producto
    {
        return $this->producto;
    }

    public function setProducto(?Producto $producto): static
    {
        $this->producto = $producto;
        return $this;
    }

    public function getCantidad(): ?int
    {
        return $this->cantidad;
    }

    public function setCantidad(int $cantidad): static
    {
        $this->cantidad = $cantidad;
        return $this;
    }

    public function getPrecioUnitario(): ?string
    {
        return $this->precioUnitario;
    }

    public function setPrecioUnitario(string $precioUnitario): static
    {
        $this->precioUnitario = $precioUnitario;
        return $this;
    }

    public function getSubtotal(): float
    {
        return (float)$this->precioUnitario * $this->cantidad;
    }
}
