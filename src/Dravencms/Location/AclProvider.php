<?php declare(strict_types=1);

namespace Dravencms\Location;

use Dravencms\User\Acl\{AclPermission, AclProviderInterface};

final class AclProvider implements AclProviderInterface
{
    public function getPermissions(): iterable
    {
        yield new AclPermission('location', 'edit', 'location.acl.permissions.edit.label', resourceLabel: 'location.acl.resource');
        yield new AclPermission('location', 'delete', 'location.acl.permissions.delete.label', resourceLabel: 'location.acl.resource');
        yield new AclPermission('location', 'streetEdit', 'location.acl.permissions.streetEdit.label', resourceLabel: 'location.acl.resource');
        yield new AclPermission('location', 'streetDelete', 'location.acl.permissions.streetDelete.label', resourceLabel: 'location.acl.resource');
        yield new AclPermission('location', 'cityEdit', 'location.acl.permissions.cityEdit.label', resourceLabel: 'location.acl.resource');
        yield new AclPermission('location', 'cityDelete', 'location.acl.permissions.cityDelete.label', resourceLabel: 'location.acl.resource');
        yield new AclPermission('location', 'zipCodeEdit', 'location.acl.permissions.zipCodeEdit.label', resourceLabel: 'location.acl.resource');
        yield new AclPermission('location', 'zipCodeDelete', 'location.acl.permissions.zipCodeDelete.label', resourceLabel: 'location.acl.resource');
        yield new AclPermission('location', 'countryEdit', 'location.acl.permissions.countryEdit.label', resourceLabel: 'location.acl.resource');
        yield new AclPermission('location', 'countryDelete', 'location.acl.permissions.countryDelete.label', resourceLabel: 'location.acl.resource');
        yield new AclPermission('location', 'regionEdit', 'location.acl.permissions.regionEdit.label', resourceLabel: 'location.acl.resource');
        yield new AclPermission('location', 'regionDelete', 'location.acl.permissions.regionDelete.label', resourceLabel: 'location.acl.resource');
    }
}
