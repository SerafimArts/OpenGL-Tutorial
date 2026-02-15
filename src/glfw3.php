<?php

const GLFW_SAMPLES = 0x0002100D;
const GLFW_CONTEXT_VERSION_MAJOR = 0x00022002;
const GLFW_CONTEXT_VERSION_MINOR = 0x00022003;
const GLFW_OPENGL_FORWARD_COMPAT = 0x00022006;
const GLFW_OPENGL_PROFILE = 0x00022008;
const GLFW_OPENGL_CORE_PROFILE = 0x00032001;

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
void glfwWindowHint(int hint, int value);
GLFWwindow* glfwCreateWindow(int width, int height, const char* title, GLFWmonitor* monitor, GLFWwindow* share);
void glfwDestroyWindow(GLFWwindow* window);
int glfwWindowShouldClose(GLFWwindow* window);
void glfwPollEvents(void);
void glfwMakeContextCurrent(GLFWwindow* window);
void glfwSwapBuffers(GLFWwindow* window);
