<?php

namespace App\Entity;

use App\Repository\UsuarioRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UsuarioRepository::class)]
#[ORM\Table(name: 'usuarios')]
#[ORM\UniqueConstraint(name: 'nombre_usuario', columns: ['nombre_usuario'])]
#[ORM\UniqueConstraint(name: 'email', columns: ['email'])]
#[UniqueEntity(fields: ['nombreUsuario'], message: 'Ya existe una cuenta con este nombre de usuario')]
#[UniqueEntity(fields: ['email'], message: 'Ya existe una cuenta con este correo electrónico')]
class Usuario implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(name: 'nombre_usuario', length: 50, unique: true)]
    private ?string $nombreUsuario = null;

    #[ORM\Column(length: 100, unique: true)]
    private ?string $email = null;

    #[ORM\Column(length: 255)]
    private ?string $password = null;

    #[ORM\Column(type: 'string', length: 20, options: ['default' => 'normal'])]
    private ?string $tipo = 'normal';

    #[ORM\Column(length: 255, nullable: true, options: ['default' => 'default_avatar.png'])]
    private ?string $avatar = 'default_avatar.png';

    #[ORM\Column]
    private array $roles = [];

    #[ORM\Column(name: 'fecha_registro', type: Types::DATETIME_MUTABLE, nullable: true, options: ['default' => 'CURRENT_TIMESTAMP'])]
    private ?\DateTimeInterface $fechaRegistro = null;

    public function __construct()
    {
        $this->fechaRegistro = new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNombreUsuario(): ?string
    {
        return $this->nombreUsuario;
    }

    public function setNombreUsuario(string $nombreUsuario): static
    {
        $this->nombreUsuario = $nombreUsuario;
        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;
        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;
        return $this;
    }

    public function getTipo(): ?string
    {
        return $this->tipo;
    }

    public function setTipo(string $tipo): static
    {
        $this->tipo = $tipo;
        return $this;
    }

    public function getAvatar(): ?string
    {
        return $this->avatar;
    }

    public function setAvatar(?string $avatar): static
    {
        $this->avatar = $avatar;
        return $this;
    }

    public function getFechaRegistro(): ?\DateTimeInterface
    {
        return $this->fechaRegistro;
    }

    public function setFechaRegistro(?\DateTimeInterface $fechaRegistro): static
    {
        $this->fechaRegistro = $fechaRegistro;
        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    public function hasRole(string $role): bool
    {
        return in_array($role, $this->roles);
    }

    public function eraseCredentials(): void
    {
        // No sensitive data to erase
    }

    public function getUserIdentifier(): string
    {
        return $this->email;
    }

    public function __toString(): string
    {
        return $this->nombreUsuario ?? '';
    }
}
