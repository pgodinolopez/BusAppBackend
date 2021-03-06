<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;
/**
 * @ORM\Entity(repositoryClass="App\Repository\RutaFavoritaRepository")
 */
class RutaFavorita
{
     /**
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=100)
     */

    protected $idlinea;

    /**
     * @ORM\Column(type="string", length=100)
     */

    protected $codigo;

    /**
     * @ORM\Column(type="string", length=100)
     */
    protected $dias;

    /**
     * @ORM\Column(type="string", length=181, nullable=true)
     */
    protected $observaciones;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    protected $horaSalida;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    protected $horaLlegada;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $operadores;

    /**
     * @ORM\Column(type="decimal", precision=5, scale=2, length=100, nullable=true)
     */
    protected $precio_billete_sencillo;

    /**
     * @ORM\Column(type="decimal", precision=5, scale=2, length=100, length=100, nullable=true)
     */
    protected $precio_tarjeta;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $tiempo_estimado;

    /**
     * @ORM\Column(type="boolean", length=100, nullable=true)
     */
    protected $pmr;

    /**
     * @ORM\Column(type="json", length=300, nullable=true)
     */
    protected $linea;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    protected $origen;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    protected $destino;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Usuarios", inversedBy="rutasFavoritas")
     * @ORM\JoinColumn(name="id_usuario", referencedColumnName="id")
     * @Serializer\Exclude()
     */
    protected $id_usuario;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdlinea(): ?string
    {
        return $this->idlinea;
    }

    public function setIdlinea(string $idlinea): self
    {
        $this->idlinea = $idlinea;

        return $this;
    }

    public function getCodigo(): ?string
    {
        return $this->codigo;
    }

    public function setCodigo(string $codigo): self
    {
        $this->codigo = $codigo;

        return $this;
    }

    public function getDias(): ?string
    {
        return $this->dias;
    }

    public function setDias(?string $dias): self
    {
        $this->dias = $dias;

        return $this;
    }

    public function getObservaciones(): ?string
    {
        return $this->observaciones;
    }

    public function setObservaciones(?string $observaciones): self
    {
        $this->observaciones = $observaciones;

        return $this;
    }

    public function getHoraSalida(): ?string
    {
        return $this->horaSalida;
    }

    public function setHoraSalida(?string $horaSalida): self
    {
        $this->horaSalida = $horaSalida;

        return $this;
    }

    public function getHoraLlegada(): ?string
    {
        return $this->horaLlegada;
    }

    public function setHoraLlegada(?string $horaLlegada): self
    {
        $this->horaLlegada = $horaLlegada;

        return $this;
    }

    public function getOperadores(): ?string
    {
        return $this->operadores;
    }

    public function setOperadores(?string $operadores): self
    {
        $this->operadores = $operadores;

        return $this;
    }

    public function getPrecio_billete_sencillo()
    {
        return $this->precio_billete_sencillo;
    }

    public function setPrecio_billete_sencillo($precio_billete_sencillo): self
    {
        $this->precio_billete_sencillo = $precio_billete_sencillo;

        return $this;
    }

    public function getPrecioTarjeta()
    {
        return $this->precio_tarjeta;
    }

    public function setPrecioTarjeta($precio_tarjeta): self
    {
        $this->precio_tarjeta = $precio_tarjeta;

        return $this;
    }

    public function getTiempoEstimado()
    {
        return $this->tiempo_estimado;
    }

    public function setTiempoEstimado($tiempo_estimado): self
    {
        $this->tiempo_estimado = $tiempo_estimado;

        return $this;
    }

    public function getPmr(): ?bool
    {
        return $this->pmr;
    }

    public function setPmr(?bool $pmr): self
    {
        $this->pmr = $pmr;

        return $this;
    }

    public function getLinea()
    {
        return $this->linea;
    }

    public function setLinea($linea): self
    {
        $this->linea = $linea;

        return $this;
    }

    public function getIdUsuario(): ?Usuarios
    {
        return $this->id_usuario;
    }

    public function setIdUsuario(?Usuarios $id_usuario): self
    {
        $this->id_usuario = $id_usuario;

        return $this;
    }

    public function getOrigen(): ?string
    {
        return $this->origen;
    }

    public function setOrigen(?string $origen): self
    {
        $this->origen = $origen;

        return $this;
    }

    public function getDestino(): ?string
    {
        return $this->destino;
    }

    public function setDestino(?string $destino): self
    {
        $this->destino = $destino;

        return $this;
    }
}