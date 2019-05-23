<?php
 
namespace App\Entity;
 
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use DateTime;
use Symfony\Component\Security\Core\User\UserInterface;
use JMS\Serializer\Annotation as Serializer;
 
/**
 * Usuarios
 *
 * @ORM\Table(name="usuarios");
 * @ORM\Entity(repositoryClass="App\Repository\UsuariosRepository");
 * @ORM\HasLifecycleCallbacks()
 */
class Usuarios implements UserInterface
{
    /**
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;
 
    /**
     * @ORM\Column(name="nombre", type="string", length=150)
     */
    protected $nombre;

    /**
     * @ORM\Column(name="telefono", type="string", length=150)
     */
    protected $telefono;

    /**
     * @ORM\Column(name="token_dispositivo", type="string", length=300)
     */
    protected $token_dispositivo;

    protected $salt;

    /**
     * @ORM\Column(type="string", length=191, unique=true)
     */
    protected $email;
 
    /**
     * @ORM\Column(name="password", type="string", length=191)
     * @Serializer\Exclude()
     */
    protected $password;
 
    /**
     * @var string
     */
    protected $plainPassword;
 
    // /**
    //  * @var array
    //  *
    //  * @ORM\Column(name="roles", type="json_array")
    //  */
    // protected $roles = [];
    
    /**
     * @ORM\Column(name="created_at", type="datetime")
     */
    protected $createdAt;
 
    /**
     * @ORM\Column(name="updated_at", type="datetime")
     */
    protected $updatedAt;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\RutaFavorita", mappedBy="id_usuario")
     */
    private $rutasFavoritas;
 
 
    public function __construct()
    {
        $this->rutasFavoritas = new ArrayCollection();
    }
 
    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getNombre()
    {
        return $this->nombre;
    }
 
    /**
     * @param mixed $nombre
     * @return self
     */
    public function setNombre($nombre)
    {
        $this->nombre = $nombre;
 
        return $this;
    }

    /**
     * @return mixed
     */
    public function getTokenDispositivo()
    {
        return $this->token_dispositivo;
    }
 
    /**
     * @param mixed $token_dispositivo
     * @return self
     */
    public function setTokenDispositivo($token_dispositivo)
    {
        $this->token_dispositivo = $token_dispositivo;
 
        return $this;
    }
    
    /**
     * @return mixed
     */
    public function getTelefono()
    {
        return $this->telefono;
    }
 
    /**
     * @param mixed $telefono
     * @return self
     */
    public function setTelefono($telefono)
    {
        $this->telefono = $telefono;
 
        return $this;
    }

    /**
     * Set email
     *
     * @param string $email
     *
     * @return User
     */
    public function setEmail($email)
    {
        $this->email = $email;
 
        return $this;
    }
 
    /**
     * Get email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }
    
    /**
     * Devuelve el id del usuario, es necesario utilizar getUsername para utilizar la interfaz UserInterface
     * 
     * @return mixed 
     */
    public function getUsername()
    {
        return $this->id;
    }
 
    /**
     * @return mixed
     */
    public function getPassword()
    {
        return $this->password;
    }
 
    /**
     * @param mixed $password
     * @return self
     */
    public function setPassword($password)
    {
        $this->password = $password;
 
        return $this;
    }
 
    /**
     * @return string
     */
    public function getPlainPassword()
    {
        return $this->plainPassword;
    }
 
    /**
     * @param $plainPassword
     */
    public function setPlainPassword($plainPassword)
    {
        $this->plainPassword = $plainPassword;
 
        $this->password = null;
    }
    
    public function getSalt() {}

    /**
     * Set roles
     *
     * @param array $roles
     *
     * @return User
     */
    public function setRoles($roles)
    {
        $this->roles = $roles;
 
        return $this;
    }
    
    /**
     * Get roles
     *
     * @return array
     */
    public function getRoles()
    {
        return ["ROLE_USER"];
    }
 
    public function eraseCredentials() {}
 
    /**
     * @return mixed
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }
 
    /**
     * @param mixed $createdAt
     * @return self
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;
 
        return $this;
    }
 
    /**
     * @return mixed
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }
 
    /**
     * @param mixed $updatedAt
     * @return self
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;
 
        return $this;
    }
 
    /**
     * @ORM\PrePersist
     * @ORM\PreUpdate
     */
    public function updatedTimestamps()
    {
        $dateTimeNow = new DateTime('now');
        $this->setUpdatedAt($dateTimeNow);
        if ($this->getCreatedAt() === null) {
            $this->setCreatedAt($dateTimeNow);
        }
    }

    /**
     * @return Collection|RutaFavorita[]
     */
    public function getRutasFavoritas(): Collection
    {
        return $this->rutasFavoritas;
    }
 
}
