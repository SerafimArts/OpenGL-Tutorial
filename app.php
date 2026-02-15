<?php

// $_SERVER['GLFW3_LIB'] = __DIR__ . '/../glfw3.dll';

require __DIR__ . '/src/glfw3.php';
require __DIR__ . '/src/glfw3.helpers.php';
require __DIR__ . '/src/shaders.php';

/* Initialize the library */
if (!glfwInit()) {
    exit(-1);
}

glfwWindowHint(GLFW_SAMPLES, 4);
glfwWindowHint(GLFW_CONTEXT_VERSION_MAJOR, 3);
glfwWindowHint(GLFW_CONTEXT_VERSION_MINOR, 3);
glfwWindowHint(GLFW_OPENGL_FORWARD_COMPAT, GL_TRUE);
glfwWindowHint(GLFW_OPENGL_PROFILE, GLFW_OPENGL_CORE_PROFILE);

/* Create a windowed mode window and its OpenGL context */
$window = glfwCreateWindow(640, 480, 'Hello World', null, null);

if (!$window) {
    glfwTerminate();
    exit(-1);
}

/* Make the window's context current */
glfwMakeContextCurrent($window);

// Dark blue background
glClearColor(0.0, 0.0, 0.4, 0.0);

// Create and compile our GLSL program from the shaders
$programId = shader_program_from_file(__DIR__ . '/example.vert', __DIR__ . '/example.frag');

$vao = glfwNew('GLuint');
glGenVertexArrays(1, FFI::addr($vao));
glBindVertexArray($vao->cdata);
{
    $vertices = glfwFloatArray([
        -1.0, -1.0, 0.0,
        1.0, -1.0, 0.0,
        0.0,  1.0, 0.0,
    ]);

    $vbo = glfwNew('GLuint');
    glGenBuffers(1, FFI::addr($vbo));
    glBindBuffer(GL_ARRAY_BUFFER, $vbo->cdata);
    glBufferData(GL_ARRAY_BUFFER, FFI::sizeof($vertices), $vertices, GL_STATIC_DRAW);
    glVertexAttribPointer(0, 3, GL_FLOAT, GL_FALSE, 0, null);
    glEnableVertexAttribArray(0);
}
glBindVertexArray(0);


/* Loop until the user closes the window */
while (!glfwWindowShouldClose($window)) {
    // Clear the screen
    glClear(GL_COLOR_BUFFER_BIT | GL_DEPTH_BUFFER_BIT);

    // Use our shader
    glUseProgram($programId);
    glBindVertexArray($vao->cdata);

    // Draw the triangle!
    // 3 indices starting at 0 -> 1 triangle
    glDrawArrays(GL_TRIANGLES, 0, 3);

    // Swap buffers
    glfwSwapBuffers($window);
    glfwPollEvents();
}

// Cleanup VBO
glDeleteBuffers(1, FFI::addr($vbo));
glDeleteVertexArrays(1, FFI::addr($vao));
glDeleteProgram($programId);

// Close the OpenGL window and terminate GLFW
glfwTerminate();
