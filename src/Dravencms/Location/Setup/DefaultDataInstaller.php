<?php declare(strict_types=1);

namespace Dravencms\Location\Setup;

use Composer\InstalledVersions;
use Doctrine\ORM\EntityManagerInterface;
use Dravencms\Model\Location\Entities\{Country, City, ZipCode, Street, StreetNumber};

final class DefaultDataInstaller
{
    public function __construct(private EntityManagerInterface $manager) {}

    public function install(bool $exampleAddress = false): void
    {
        $this->manager->wrapInTransaction(function () use ($exampleAddress): void {
            $countries = require InstalledVersions::getInstallPath('umpirsky/country-list') . '/data/en_US/country.php';
            foreach ($countries as $code => $name) {
                $this->ensure(Country::class, ['code' => $code], static fn() => new Country($name, $code));
            }
            $this->manager->flush();
            if ($exampleAddress) {
                $country = $this->manager->getRepository(Country::class)->findOneBy(['code' => 'CZ']);
                $city = $this->ensure(City::class, ['country' => $country, 'name' => 'Olomouc'], static fn() => new City($country, 'Olomouc'));
                $zip = $this->ensure(ZipCode::class, ['city' => $city, 'name' => '77900'], static fn() => new ZipCode($city, '77900'));
                $street = $this->ensure(Street::class, ['zipCode' => $zip, 'name' => 'Brněnská'], static fn() => new Street($zip, 'Brněnská'));
                $this->ensure(StreetNumber::class, ['street' => $street, 'name' => '54'], static fn() => new StreetNumber($street, '54'));
            }
        });
    }

    private function ensure(string $class, array $criteria, callable $create): object
    {
        $entity = $this->manager->getRepository($class)->findOneBy($criteria);
        if ($entity === null) {
            $entity = $create(); $this->manager->persist($entity);
            // Following address components need the parent's ID for their lookup.
            if ($class !== Country::class) { $this->manager->flush(); }
        }
        return $entity;
    }
}
