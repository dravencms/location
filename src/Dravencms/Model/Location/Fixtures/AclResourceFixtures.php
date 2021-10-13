<?php declare(strict_types = 1);
/**
 * Copyright (C) 2016 Adam Schubert <adam.schubert@sg1-game.net>.
 */

namespace Dravencms\Model\Location\Fixtures;

use Dravencms\Model\User\Entities\AclResource;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Persistence\ObjectManager;

class AclResourceFixtures extends AbstractFixture
{
    /**
     * Load data fixtures with the passed EntityManager
     *
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager): void
    {
        if (!class_exists(AclResource::class)) {
            trigger_error('dravencms/user module not found, dravencms/location module won\'t install ACL Resource', E_USER_NOTICE);
            return;
        }

        $resources = [
            'location' => 'Location'
        ];
        foreach ($resources AS $resourceName => $resourceDescription)
        {
            $aclResource = new AclResource($resourceName, $resourceDescription);
            $manager->persist($aclResource);
            $this->addReference('user-acl-resource-'.$resourceName, $aclResource);
        }
        $manager->flush();
    }
}