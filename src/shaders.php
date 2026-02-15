<?php

declare(strict_types=1);

use FFI\CData;

function load_file(string $pathname): CData
{
    $content = file_get_contents($pathname) . "\0";
    $size = strlen($content);

    $result = glfwNew("char[$size]", false);
    FFI::memcpy($result, $content, $size);

    return $result;
}

function shader_get_error(int $shaderId): ?string
{
    $errorLength = glfwNew('int');
    $errorLength->cdata = 0;

    glGetShaderiv($shaderId, GL_INFO_LOG_LENGTH, FFI::addr($errorLength));

    if ($errorLength->cdata > 0) {
        $message = glfwNew("char[$errorLength->cdata]");
        glGetShaderInfoLog($shaderId, $errorLength->cdata, null, $message);

        return trim(FFI::string($message, $errorLength->cdata));
    }

    return null;
}

function shader_from_file(string $pathname, int $type): int
{
    $shaderId = glCreateShader($type);
    $shaderSource = glfwCast('char*', $shader = load_file($pathname));

    $errorLength = glfwNew('int');
    $errorLength->cdata = 0;

    // Compile Shader
    echo sprintf("Compiling Shader: %s\n", $pathname);
    glShaderSource($shaderId, 1, FFI::addr($shaderSource), null);
    glCompileShader($shaderId);

    if (($errorMessage = shader_get_error($shaderId)) !== null) {
        throw new RuntimeException($errorMessage);
    }

    FFI::free(FFI::addr($shader));

    return $shaderId;
}

function shader_program_from_file(string $vertex, string $fragment): int
{
    $vertexShaderId = shader_from_file($vertex, GL_VERTEX_SHADER);
    $fragmentShaderId = shader_from_file($fragment, GL_FRAGMENT_SHADER);

    echo sprintf("Linking Shader Program...\n");
    $shaderProgramId = glCreateProgram();
    glAttachShader($shaderProgramId, $vertexShaderId);
    glAttachShader($shaderProgramId, $fragmentShaderId);
    glLinkProgram($shaderProgramId);

    if (($errorMessage = shader_get_error($shaderProgramId)) !== null) {
        throw new RuntimeException($errorMessage);
    }

    glDeleteShader($vertexShaderId);
    glDeleteShader($fragmentShaderId);

    return $shaderProgramId;
}
