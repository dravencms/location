<?php declare(strict_types = 1);

namespace Dravencms\AdminModule\Components\Location\CityForm;

use Dravencms\Model\Location\Entities\City;

/**
 * Copyright (C) 2016 Adam Schubert <adam.schubert@sg1-game.net>.
 */
interface CityFormFactory
{
    /**
     * @param City|null $city
     * @return CityForm
     */
    public function create(City $city = null): CityForm;
}