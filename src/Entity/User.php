<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * @ORM\Table(name="rw_user")
 *
 * @UniqueEntity("email", message="user.email.unique")
 * @UniqueEntity("username", message="user.username.unique")
 */
class User implements UserInterface
{
    use TimestampableEntity;

    /**
     * @var int
     *
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var string|null
     *
     * @ORM\Column(type="string", length=255, unique=true)
     *
     * @Assert\NotBlank(message="user.email.not_blank")
     * @Assert\Email(message="user.email.email")
     */
    private $email;

    /**
     * @var string|null
     *
     * @ORM\Column(type="string", length=255)
     *
     * @Assert\NotBlank(message="user.password.not_blank")
     * @Assert\Length(min="8", minMessage="user.password.length.min")
     */
    private $password;

    /**
     * @var string|null
     *
     * @ORM\Column(type="string", length=255, unique=true)
     *
     * @Assert\NotBlank(message="user.username.not_blank")
     * @Assert\Length(
     *     min="1",
     *     max="20",
     *     minMessage="user.username.length.min",
     *     maxMessage="user.username.length.max"
     * )
     */
    private $username;

    /**
     * @var string|null
     *
     * @ORM\Column(type="text", nullable=true)
     */
    private $bio;

    /**
     * @var string|null
     *
     * @ORM\Column(type="text", nullable=true)
     *
     * @Assert\Url(message="user.image.url")
     */
    private $image;

    /**
     * @var ArrayCollection|User[]
     *
     * @ORM\ManyToMany(targetEntity="App\Entity\User", mappedBy="followers")
     */
    private $followed;

    /**
     * @var ArrayCollection|User[]
     *
     * @ORM\ManyToMany(targetEntity="App\Entity\User", inversedBy="followed")
     * @ORM\JoinTable(
     *   name="rw_user_follower",
     *   joinColumns={@ORM\JoinColumn(name="user_id", referencedColumnName="id")},
     *   inverseJoinColumns={@ORM\JoinColumn(name="follower_id", referencedColumnName="id")}
     * )
     */
    private $followers;

    /**
     * @var ArrayCollection|Article[]
     *
     * @ORM\ManyToMany(targetEntity="App\Entity\Article", inversedBy="favoritedBy")
     * @ORM\JoinTable(name="rw_user_favorite")
     */
    private $favorites;

    public function __construct()
    {
        $this->followed = new ArrayCollection();
        $this->followers = new ArrayCollection();
        $this->favorites = new ArrayCollection();
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return \sprintf('%s', $this->email);
    }

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return string|null
     */
    public function getEmail(): ?string
    {
        return $this->email;
    }

    /**
     * @param string|null $email
     */
    public function setEmail(?string $email): void
    {
        $this->email = $email;
    }

    /**
     * @return string|null
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    /**
     * @param string|null $password
     */
    public function setPassword(?string $password): void
    {
        $this->password = $password;
    }

    /**
     * @return string|null
     */
    public function getUsername(): ?string
    {
        return $this->username;
    }

    /**
     * @param string|null $username
     */
    public function setUsername(?string $username): void
    {
        $this->username = $username;
    }

    /**
     * @return string|null
     */
    public function getBio(): ?string
    {
        return $this->bio;
    }

    /**
     * @param string|null $bio
     */
    public function setBio(?string $bio): void
    {
        $this->bio = $bio;
    }

    /**
     * @return string|null
     */
    public function getImage(): ?string
    {
        return $this->image;
    }

    /**
     * @param string|null $image
     */
    public function setImage(?string $image): void
    {
        $this->image = $image;
    }

    /**
     * @return string[]
     */
    public function getRoles(): array
    {
        return ['ROLE_USER'];
    }

    /**
     * {@inheritdoc}
     */
    public function getSalt(): ?string
    {
        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function eraseCredentials(): void
    {
    }

    /**
     * @param User $user
     *
     * @return bool
     */
    public function follows(User $user): bool
    {
        return $this->followed->contains($user);
    }

    /**
     * @param User $user
     */
    public function follow(User $user): void
    {
        if ($user->getFollowers()->contains($this)) {
            return;
        }

        $user->getFollowers()->add($this);
    }

    /**
     * @param User $user
     */
    public function unfollow(User $user): void
    {
        if (!$user->getFollowers()->contains($this)) {
            return;
        }

        $user->getFollowers()->removeElement($this);
    }

    /**
     * @return ArrayCollection|User[]
     */
    public function getFollowers()
    {
        return $this->followers;
    }

    /**
     * @param ArrayCollection|User[] $followers
     */
    public function setFollowers($followers): void
    {
        $this->followers = $followers;
    }

    /**
     * @return ArrayCollection|User[]
     */
    public function getFolloweds()
    {
        return $this->followed;
    }

    /**
     * @return ArrayCollection|Article[]
     */
    public function getFavorites()
    {
        return $this->favorites;
    }

    /**
     * @param ArrayCollection|Article[] $favorites
     */
    public function setFavorites($favorites): void
    {
        $this->favorites = $favorites;
    }

    /**
     * @param Article $article
     *
     * @return bool
     */
    public function hasFavorite(Article $article): bool
    {
        return $this->favorites->contains($article);
    }

    /**
     * @param Article $article
     */
    public function addToFavorites(Article $article): void
    {
        if ($this->favorites->contains($article)) {
            return;
        }

        $this->favorites->add($article);
    }

    /**
     * @param Article $article
     */
    public function removeFromFavorites(Article $article): void
    {
        if (!$this->favorites->contains($article)) {
            return;
        }

        $this->favorites->removeElement($article);
    }
}
