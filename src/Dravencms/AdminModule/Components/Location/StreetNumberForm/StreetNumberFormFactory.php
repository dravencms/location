<?php

namespace Dravencms\AdminModule\Components\Location\StreetNumberForm;

use Dravencms\Model\Location\Entities\StreetNumber;

/**
 * Copyright (C) 2016 Adam Schubert <adam.schubert@sg1-game.net>.
 */
interface StreetNumberFormFactory
{
    /**
     * @param StreetNumber|null $streetNumber
     * @return StreetNumberForm
     */
    public function create(StreetNumber $streetNumber = null);
}