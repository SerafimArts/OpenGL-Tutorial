<?php

declare(strict_types=1);

use FFI\Preprocessor\Directive\ObjectLikeDirective;
use FFI\Preprocessor\Preprocessor;

require __DIR__ . '/vendor/autoload.php';


// GLFW3
echo "Loading GLFW 3.3 headers\n";
$glfw3Preprocessor = new Preprocessor();
$glfw3Preprocessor->add('stddef.h', '');
$glfw3Preprocessor->add('stdint.h', '');
$glfw3Preprocessor->define('GLFW_INCLUDE_NONE');

$glfw3Assembly = $glfw3Preprocessor->process(new SplFileInfo(__DIR__ . '/glfw/glfw3.h'));


// OPENGL
echo "Loading OpenGL 4.6 headers\n";
$openGlPreprocessor = new Preprocessor();
$openGlPreprocessor->add('stdint.h', '');
$openGlPreprocessor->include(__DIR__ . '/opengl');
$openGlPreprocessor->define('KHRONOS_SUPPORT_FLOAT', '1');
$openGlPreprocessor->define('KHRONOS_SUPPORT_INT64', '1');

$openGlAssembly = $openGlPreprocessor->process(new SplFileInfo(__DIR__ . '/opengl/glcorearb.h'));


// Generate Result
$result = fopen(__DIR__ . '/../src/glfw3.php', 'wb+');

fwrite($result, <<<'HEADER'
    <?php

    if (defined('GLFW_INSTANCE')) {
        return GLFW_INSTANCE;
    }

    // GLFW Constants

    HEADER);


// Add GLFW Constants
echo "Generate GLFW constants\n";
foreach ($glfw3Assembly->directives as $name => $directive) {
    $isConstant = str_starts_with($name, 'GLFW_')
        && $directive instanceof ObjectLikeDirective
        && $directive->getBody() !== '';

    if (!$isConstant) {
        continue;
    }

    fwrite($result, sprintf("const %s = %s;\n", $name, $directive->getBody()));
}

// Add OpenGL Constants
echo "Generate OpenGL constants\n";
foreach ($openGlAssembly->directives as $name => $directive) {
    $isConstant = str_starts_with($name, 'GL_')
        && $directive instanceof ObjectLikeDirective
        && $directive->getBody() !== '';

    if (!$isConstant) {
        continue;
    }

    // TODO uint64 required
    if ($name === 'GL_TIMEOUT_IGNORED') {
        continue;
    }

    $value = $directive->getBody();

    // uint32 max fix
    if ($value === '0xFFFFFFFFu') {
        $value = '0xFFFFFFFF';
    }

    fwrite($result, sprintf("const %s = %s;\n", $name, $value));
}


fwrite($result, <<<'BODY'

    define('GLFW_INSTANCE', FFI::cdef(
        code: (string) file_get_contents(__FILE__, offset: __COMPILER_HALT_OFFSET__),
        lib: $_SERVER['GLFW3_LIB'] ?? match (PHP_OS_FAMILY) {
            'Windows' => 'glfw3.dll',
            'Linux' => 'libglfw.so.3',
            'Darwin' => 'glfw3.dylib',
        },
    ));

    BODY);


// Generate GLFW function stubs
echo "Generate GLFW function stubs\n";

preg_match_all(
    pattern: '/GLFWAPI\h+(.+?)\h+(glfw.+?)\h*\((.+?)\);/',
    subject: file_get_contents(__DIR__ . '/glfw/glfw3.h'),
    matches: $glfw3Matches,
    flags: PREG_SET_ORDER,
);

foreach ($glfw3Matches as [1 => $type, 2 => $function]) {
    if ($type === 'void') {
        fwrite($result, <<<FUNCTION

        function $function(mixed ...\$args): void
        {
            static \$function = GLFW_INSTANCE->$function;

            \$function(...\$args);
        }

        FUNCTION);
        continue;
    }

    fwrite($result, <<<FUNCTION

        function $function(mixed ...\$args): mixed
        {
            static \$function = GLFW_INSTANCE->$function;

            return \$function(...\$args);
        }

        FUNCTION);
}


// Generate OpenGL function stubs
echo "Generate OpenGL function stubs\n";

preg_match_all(
    pattern: '/GLAPI\h+(.+?)\h+(gl.+?)\h*\((.+?)\);/',
    subject: file_get_contents(__DIR__ . '/opengl/glcorearb.h'),
    matches: $openglMatches,
    flags: PREG_SET_ORDER,
);

foreach ($openglMatches as [1 => $type, 2 => $function]) {
    $proc = 'PFN' . \strtoupper($function) . 'PROC';

    if ($type === 'void') {
        fwrite($result, <<<FUNCTION

        function $function(mixed ...\$args): void
        {
            static \$function = GLFW_INSTANCE->cast(
                '$proc',
                GLFW_INSTANCE->glfwGetProcAddress('$function'),
            );

            \$function(...\$args);
        }

        FUNCTION);
        continue;
    }

    fwrite($result, <<<FUNCTION

        function $function(mixed ...\$args): mixed
        {
            static \$function = GLFW_INSTANCE->cast(
                '$proc',
                GLFW_INSTANCE->glfwGetProcAddress('$function'),
            );

            return \$function(...\$args);
        }

        FUNCTION);

}

fwrite($result, <<<'FOOTER'

    return GLFW_INSTANCE;

    __halt_compiler();


    FOOTER);

echo "Done\n";
fwrite($result, $glfw3Assembly . "\n\n" . $openGlAssembly);
