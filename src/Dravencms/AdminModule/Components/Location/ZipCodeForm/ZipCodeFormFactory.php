<?php

namespace Dravencms\AdminModule\Components\Location\ZipCodeForm;

use Dravencms\Model\Location\Entities\ZipCode;

/**
 * Copyright (C) 2016 Adam Schubert <adam.schubert@sg1-game.net>.
 */
interface ZipCodeFormFactory
{
    /**
     * @param ZipCode|null $zipCode
     * @return ZipCodeForm
     */
    public function create(ZipCode $zipCode = null);
}