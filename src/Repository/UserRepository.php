<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;

/**
 * Repository pour l'entité User.
 *
 * @extends ServiceEntityRepository<User> Déclare que ce repository gère l'entité User.
 * @implements PasswordUpgraderInterface<User> Implémente l'interface PasswordUpgraderInterface pour mettre à jour les mots de passe.
 */
class UserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        // Appelle le constructeur de la classe parente avec le registre de gestionnaire d'entités et l'entité gérée.
        parent::__construct($registry, User::class);
    }

    /**
     * Méthode pour mettre à jour le mot de passe de l'utilisateur.
     *
     * @param PasswordAuthenticatedUserInterface $user L'utilisateur dont le mot de passe doit être mis à jour.
     * @param string $newHashedPassword Le nouveau mot de passe hashé.
     *
     * @throws UnsupportedUserException Si l'utilisateur n'est pas une instance de User.
     */
    
    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void
    {
        // Vérifie si l'utilisateur est une instance de User, sinon lance une exception.
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', $user::class));
        }

        // Met à jour le mot de passe de l'utilisateur avec le nouveau mot de passe hashé.
        $user->setPassword($newHashedPassword);

        // Persiste les changements dans la base de données.
        $this->getEntityManager()->persist($user);
        $this->getEntityManager()->flush();
    }
}