<?php

namespace Sayla\Contract;

interface SqlBuilder
{
    public function getBindings(): array;

    public function toSql(): string;
}