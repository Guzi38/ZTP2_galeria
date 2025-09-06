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

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    protected function supports(string $attribute, $subject): bool
    {
        return in_array($attribute, [self::EDIT, self::VIEW, self::DELETE])
            && $subject instanceof Photo;
    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
        if (!$user instanceof UserInterface) {
            return false; // niezalogowany → brak dostępu
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
     * Sprawdza czy użytkownik jest autorem albo adminem.
     */
    private function canModify(Photo $photo, User $user): bool
    {
        // 🔑 porównujemy po ID zamiast po obiektach!
        if ($photo->getAuthor() && $photo->getAuthor()->getId() === $user->getId()) {
            return true;
        }

        return $this->security->isGranted('ROLE_ADMIN');
    }
}
