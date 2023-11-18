<?php

namespace Vangel\Project\Model;

final class FilesDto
{
    /**
     * @param string[] $files
     */
    public function __construct(
        public array $files
    )
    {
    }
}