<?php

/**
 * User voter.
 */

namespace App\Security\Voter;

use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Class UserVoter.
 */
class UserVoter extends Voter
{
    public const EDIT = 'EDIT';
    public const VIEW = 'VIEW';
    public const DELETE = 'DELETE';
    public const MANAGE = 'MANAGE';

    /**
     * Security helper.
     */
    private Security $security;

    /**
     * Constructor.
     *
     * @param Security $security Security helper
     */
    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    /**
     * Determines if the attribute and subject are supported by this voter.
     *
     * @param string $attribute Attribute
     * @param mixed  $subject   Subject
     *
     * @return bool Result
     */
    protected function supports(string $attribute, $subject): bool
    {
        return in_array($attribute, [self::EDIT, self::VIEW, self::DELETE, self::MANAGE], true)
            && $subject instanceof User;
    }

    /**
     * Perform a single access check operation on a given attribute, subject and token.
     *
     * @param string         $attribute Permission name
     * @param mixed          $subject   Object
     * @param TokenInterface $token     Security token
     *
     * @return bool Result
     */
    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        $loggedUser = $token->getUser();
        if (!$loggedUser instanceof UserInterface) {
            return false;
        }

        /* @var User $subject */
        switch ($attribute) {
            case self::MANAGE:
                return $this->security->isGranted('ROLE_ADMIN');
            case self::VIEW:
                return $this->canView($subject, $loggedUser);
            case self::EDIT:
                return $this->canEdit($subject, $loggedUser);
            case self::DELETE:
                return $this->canDelete($loggedUser);
        }

        return false;
    }

    /**
     * Checks if user can edit User.
     *
     * @param User          $subject    Target user
     * @param UserInterface $loggedUser Logged-in user
     *
     * @return bool Result
     */
    private function canEdit(User $subject, UserInterface $loggedUser): bool
    {
        // user can edit himself
        if ($subject->getId() === $loggedUser->getId()) {
            return true;
        }

        // admin can edit anyone
        return $this->security->isGranted('ROLE_ADMIN');
    }

    /**
     * Checks if user can view User.
     *
     * @param User          $subject    Target user
     * @param UserInterface $loggedUser Logged-in user
     *
     * @return bool Result
     */
    private function canView(User $subject, UserInterface $loggedUser): bool
    {
        if ($subject->getId() === $loggedUser->getId()) {
            return true;
        }

        return $this->security->isGranted('ROLE_ADMIN');
    }

    /**
     * Checks if user can delete User.
     *
     * @param UserInterface $loggedUser Logged-in user
     *
     * @return bool Result
     */
    private function canDelete(UserInterface $loggedUser): bool
    {
        return $this->security->isGranted('ROLE_ADMIN');
    }
}
