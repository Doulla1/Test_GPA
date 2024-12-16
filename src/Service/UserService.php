<?php

namespace App\Service;

use App\Dto\UserDto;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

class UserService
{
    public function __construct(private EntityManagerInterface $entityManager) {}

    public function createUser(UserDto $userDto): User
    {
        try {
            $user = new User();
            $user->setPrenom($userDto->prenom);
            $user->setNom($userDto->nom);
            $user->setEmail($userDto->email);

            $this->entityManager->persist($user);
            $this->entityManager->flush();

            return $user;
        } catch (\Exception $e) {
            throw new \RuntimeException('Erreur lors de la création de l\'utilisateur.'. $e->getMessage ());
        }
    }

    public function updateUser(User $user, UserDto $userDto): User
    {
        try {
            $user->setPrenom($userDto->prenom);
            $user->setNom($userDto->nom);
            $user->setEmail($userDto->email);

            $this->entityManager->flush();

            return $user;
        } catch (\Exception $e) {
            throw new \RuntimeException('Erreur lors de la mise à jour de l\'utilisateur.');
        }
    }

    public function deleteUser(User $user): void
    {
        try {
            $this->entityManager->remove($user);
            $this->entityManager->flush();
        } catch (\Exception $e) {
            throw new \RuntimeException('Erreur lors de la suppression de l\'utilisateur.');
        }
    }
}
