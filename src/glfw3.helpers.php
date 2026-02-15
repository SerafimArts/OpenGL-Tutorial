<?php

declare(strict_types=1);

use FFI\CData;

function glfwNew(string $type, bool $owned = true): CData
{
    return GLFW_INSTANCE->new($type, $owned);
}

function glfwCast(string $type, CData $ptr): CData
{
    return GLFW_INSTANCE->cast($type, $ptr);
}

function glfwFloatArray(array $values): CData
{
    $count = count($values);
    $array = GLFW_INSTANCE->new("float[{$count}]");

    for ($i = 0; $i < $count; $i++) {
        $array[$i] = $values[$i];
    }

    return $array;
}
