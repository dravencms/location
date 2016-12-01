<?php

namespace Dravencms\AdminModule\Components\Location\StreetForm;

use Dravencms\Model\Location\Entities\Street;

/**
 * Copyright (C) 2016 Adam Schubert <adam.schubert@sg1-game.net>.
 */
interface StreetFormFactory
{
    /**
     * @param Street|null $street
     * @return StreetForm
     */
    public function create(Street $street = null);
}