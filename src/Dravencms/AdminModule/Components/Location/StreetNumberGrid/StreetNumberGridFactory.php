<?php
/**
 * Copyright (C) 2016 Adam Schubert <adam.schubert@sg1-game.net>.
 */

namespace Dravencms\AdminModule\Components\Location\StreetNumberGrid;

interface StreetNumberGridFactory
{
    /**
     * @return StreetNumberGrid
     */
    public function create();
}