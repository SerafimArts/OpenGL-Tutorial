<?php

// $_SERVER['OPENGL_LIB'] = '/usr/lib/x86_64-linux-gnu/libGL.so';
// $_SERVER['GLFW3_LIB'] = __DIR__ . '/../glfw3.dll';

$opengl = require __DIR__ . '/src/opengl.php';
$glfw   = require __DIR__ . '/src/glfw3.php';

/* Initialize the library */
if (!$glfw->glfwInit()) {
    exit(-1);
}

$glfw->glfwWindowHint(GLFW_SAMPLES, 4);
$glfw->glfwWindowHint(GLFW_CONTEXT_VERSION_MAJOR, 3);
$glfw->glfwWindowHint(GLFW_CONTEXT_VERSION_MINOR, 3);
$glfw->glfwWindowHint(GLFW_OPENGL_FORWARD_COMPAT, GL_TRUE);
$glfw->glfwWindowHint(GLFW_OPENGL_PROFILE, GLFW_OPENGL_CORE_PROFILE);

/* Create a windowed mode window and its OpenGL context */
$window = $glfw->glfwCreateWindow(640, 480, 'Hello World', null, null);

if (!$window) {
    $glfw->glfwTerminate();
    exit(-1);
}

/* Make the window's context current */
$glfw->glfwMakeContextCurrent($window);

/* Loop until the user closes the window */
while (!$glfw->glfwWindowShouldClose($window)) {
    /* Render here */
    $opengl->glClear(GL_COLOR_BUFFER_BIT);

    /* Swap front and back buffers */
    $glfw->glfwSwapBuffers($window);

    /* Poll for and process events */
    $glfw->glfwPollEvents();
}

$glfw->glfwTerminate();
