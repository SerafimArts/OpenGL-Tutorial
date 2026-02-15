<?php

const GL_COLOR_BUFFER_BIT = 0x00004000;
const GL_TRUE = 1;

return FFI::cdef(
    code: (string)file_get_contents(__FILE__, offset: __COMPILER_HALT_OFFSET__),
    lib: $_SERVER['OPENGL_LIB'] ?? match (PHP_OS_FAMILY) {
        'Windows' => 'opengl32.dll',
        'Linux' => 'libGL.so.1',
        'Darwin' => 'libGL.dylib',
    },
);

__halt_compiler();

typedef unsigned int GLbitfield;
void glClear (GLbitfield mask);
