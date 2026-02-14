<?php

return FFI::cdef(
    code: (string)file_get_contents(__FILE__, offset: __COMPILER_HALT_OFFSET__),
    lib: $_SERVER['GLFW3_LIB'] ?? match (PHP_OS_FAMILY) {
        // Install using: https://www.glfw.org/download.html
        'Windows' => 'glfw3.dll',
        'Linux' => 'libglfw.so.3',
        'Darwin' => 'glfw3.dylib',
    },
);

__halt_compiler();

typedef struct GLFWmonitor GLFWmonitor;
typedef struct GLFWwindow GLFWwindow;

int glfwInit(void);
void glfwTerminate(void);
GLFWwindow* glfwCreateWindow(int width, int height, const char* title, GLFWmonitor* monitor, GLFWwindow* share);
void glfwDestroyWindow(GLFWwindow* window);
int glfwWindowShouldClose(GLFWwindow* window);
void glfwPollEvents(void);
void glfwMakeContextCurrent(GLFWwindow* window);
void glfwSwapBuffers(GLFWwindow* window);
