<?php

namespace Redbill\AssetInterfaces;

abstract class AssetInterface
{
    /**
     * Token has to be lowercase and unique for each AssetInterface
     */
    const TOKEN = 'redbill';

    public function __construct()
    {
        $this->_connect();
    }

    abstract protected function _connect();

    abstract public function getProjects();

    abstract protected function _getProjectEntries($projectId);
}