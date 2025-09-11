<?php

/**
 * Photo voter.
 */

namespace App\Security\Voter;

use App\Entity\Photo;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Class PhotoVoter.
 */
class PhotoVoter extends Voter
{
    public const EDIT = 'EDIT';
    public const VIEW = 'VIEW';
    public const DELETE = 'DELETE';

    private Security $security;

    /**
     * Constructor.
     *
     * @param Security $security Security service
     */
    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    /**
     * Checks if the given attribute and subject are supported by this voter.
     *
     * @param string $attribute Attribute (EDIT, VIEW, DELETE)
     * @param mixed  $subject   Subject to check (should be a Photo)
     *
     * @return bool True if supported, false otherwise
     */
    protected function supports(string $attribute, $subject): bool
    {
        return in_array($attribute, [self::EDIT, self::VIEW, self::DELETE])
            && $subject instanceof Photo;
    }

    /**
     * Performs the access decision.
     *
     * @param string         $attribute Attribute being voted on
     * @param mixed          $subject   Subject (Photo entity)
     * @param TokenInterface $token     Security token
     *
     * @return bool True if granted, false otherwise
     */
    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
        if (!$user instanceof UserInterface) {
            return false;
        }

        /** @var Photo $photo */
        $photo = $subject;

        switch ($attribute) {
            case self::VIEW:
                return true;

            case self::EDIT:
            case self::DELETE:
                return $this->canModify($photo, $user);
        }

        return false;
    }

    /**
     * Checks if a user can modify the photo (author or admin).
     *
     * @param Photo $photo Photo entity
     * @param User  $user  User entity
     *
     * @return bool True if user can modify, false otherwise
     */
    private function canModify(Photo $photo, User $user): bool
    {
        if ($photo->getAuthor() && $photo->getAuthor()->getId() === $user->getId()) {
            return true;
        }

        return $this->security->isGranted('ROLE_ADMIN');
    }
}
