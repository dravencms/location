<?php
/**
 * Copyright (C) 2016 Adam Schubert <adam.schubert@sg1-game.net>.
 */

namespace Dravencms\AdminModule\Components\Location\StreetGrid;

interface StreetGridFactory
{
    /**
     * @return StreetGrid
     */
    public function create();
}