<?php

namespace Dravencms\AdminModule\Components\Location\CountryForm;

use Dravencms\Model\Location\Entities\Country;

/**
 * Copyright (C) 2016 Adam Schubert <adam.schubert@sg1-game.net>.
 */
interface CountryFormFactory
{
    /**
     * @param Country|null $country
     * @return CountryForm
     */
    public function create(Country $country = null);
}