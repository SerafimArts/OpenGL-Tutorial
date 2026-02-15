<?php

// $_SERVER['GLFW3_LIB'] = __DIR__ . '/../glfw3.dll';

require __DIR__ . '/src/glfw3.php';

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

/* Loop until the user closes the window */
while (!glfwWindowShouldClose($window)) {
    /* Render here */
    glClear(GL_COLOR_BUFFER_BIT);

    /* Swap front and back buffers */
    glfwSwapBuffers($window);

    /* Poll for and process events */
    glfwPollEvents();
}

glfwTerminate();
