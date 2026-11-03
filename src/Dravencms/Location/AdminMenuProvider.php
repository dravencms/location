<?php declare(strict_types=1);

namespace Dravencms\Location;

use Dravencms\Admin\Menu\{AdminMenuItem, AdminMenuProviderInterface};

final class AdminMenuProvider implements AdminMenuProviderInterface
{
    public function getItems(): iterable
    {
        yield new AdminMenuItem('location', 'location.menu.root', icon: 'fa-globe', order: 950);
        yield new AdminMenuItem('location.street', 'location.menu.street', destination: ':Admin:Location:Street:default', parent: 'location', icon: 'fa-road', order: 10, resource: 'location', privilege: 'streetEdit');
        yield new AdminMenuItem('location.streetnumber', 'location.menu.streetnumber', destination: ':Admin:Location:StreetNumber:default', parent: 'location', icon: 'fa-subscript', order: 20, resource: 'location', privilege: 'streetEdit');
        yield new AdminMenuItem('location.city', 'location.menu.city', destination: ':Admin:Location:City:default', parent: 'location', icon: 'fa-map-marker', order: 30, resource: 'location', privilege: 'cityEdit');
        yield new AdminMenuItem('location.zipcode', 'location.menu.zipcode', destination: ':Admin:Location:ZipCode:default', parent: 'location', icon: 'fa-map-signs', order: 40, resource: 'location', privilege: 'zipCodeEdit');
        yield new AdminMenuItem('location.country', 'location.menu.country', destination: ':Admin:Location:Country:default', parent: 'location', icon: 'fa-globe', order: 50, resource: 'location', privilege: 'countryEdit');
        yield new AdminMenuItem('location.region', 'location.menu.region', destination: ':Admin:Location:Region:default', parent: 'location', icon: 'fa-compass', order: 60, resource: 'location', privilege: 'regionEdit');
    }
}
