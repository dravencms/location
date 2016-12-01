<?php
/**
 * Copyright (C) 2016 Adam Schubert <adam.schubert@sg1-game.net>.
 */

namespace Dravencms\AdminModule\Components\Location\ZipCodeGrid;

interface ZipCodeGridFactory
{
    /**
     * @return ZipCodeGrid
     */
    public function create();
}