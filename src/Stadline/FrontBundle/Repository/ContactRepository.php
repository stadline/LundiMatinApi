<?php
/**
 * Created by PhpStorm.
 * User: nico
 * Date: 11/12/14
 * Time: 15:51
 */

namespace Stadline\FrontBundle\Repository;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use SensioLabs\Security\Exception\RuntimeException;
use Stadline\FrontBundle\Entity\Contact;
use Symfony\Component\Security\Core\Exception\AuthenticationException;

class ContactRepository extends EntityRepository
{
// <editor-fold desc="cryptage area">
    private $round = 1000;

    public function encrypt($ref, $key, $allowCreate = false) // TODO: verif fonctionnement
    {
        /** @var EntityManager $em */
        $em = $this->getEntityManager();
        /** @var Contact $user */
        $user = $this->findOneBy(array('ref' => $ref));

        if (!is_null($user)) {
            if ($user instanceof Contact) {
                return $user->getHashedRef();
            } else {
                return false;
            }
        }

        if ($allowCreate === true) {
            $user = new Contact();
            $user->setRef($ref);
            $hashedRef = crypt($ref, sprintf('$6$%d$%s', $this->round, $key));
            $user->setHashedRef($hashedRef);

            $em->persist($user);
            $em->flush();
            return $user->getHashedRef();
        }
        return false;
    }

    public function decrypt($hashedRef) // TODO: verif fonctionnement
    {
        /** @var Contact $user */
        $user = $this->findOneBy(array('hashedRef' => $hashedRef));

        if (is_null($user)) return false;
        if (!$user instanceof Contact) return false;

        return $user->getRef();
    }
// </editor-fold>
} 