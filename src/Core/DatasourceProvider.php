<?php

namespace Vangel\Project\Core;

interface DatasourceProvider
{
    function getDatasource(): Datasource;
}