<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/10/22
 * Time: 21:29
 */

if (!function_exists('getServices')) {
    /**
     * @return array
     * @throws Exception
     */
    function getServices(): array
    {
        $dir = \rabbit\App::getAlias('@services', false);
        try {
            $files = array();
            $queue = array($dir);
            while ($data = each($queue)) {
                $path = $data['value'];
                if (is_dir($path) && $handle = opendir($path)) {
                    while ($file = readdir($handle)) {
                        if ($file == '.' || $file == '..') {
                            continue;
                        }
                        $real_path = $path . '/' . $file;
                        if (is_dir($real_path)) {
                            $queue[] = $real_path;
                        } elseif (strpos($real_path, 'Service') !== false) {
                            $real_path = str_replace($dir, '', $real_path);
                            $services = str_replace('.php', '', trim(strrchr($real_path, '/'), '/'));
                            $namespace = str_replace($dir, '', $real_path);
                            $namespace = str_replace('/', '', substr($namespace, 0, strrpos($namespace, '/')));
                            $files[$services] = 'services' . '\\' . $namespace . '\\' . $services;
                        }
                    }
                }
                if ($handle) {
                    closedir($handle);
                }
            }
            return $files;
        } catch (\Exception $exc) {
            throw $exc;
        }
    }
}

if (!function_exists('getApis')) {
    /**
     * @return array
     * @throws Exception
     */
    function getApis(): array
    {
        $dir = \rabbit\App::getAlias('@apis', false);
        try {
            $files = array();
            $queue = array($dir);
            while ($data = each($queue)) {
                $path = $data['value'];
                if (is_dir($path) && $handle = opendir($path)) {
                    while ($file = readdir($handle)) {
                        if ($file == '.' || $file == '..') {
                            continue;
                        }
                        $real_path = $path . '/' . $file;
                        if (is_dir($real_path)) {
                            $queue[] = $real_path;
                        } elseif (strpos($real_path, 'Controller') !== false) {
                            $namespace = str_replace([$dir, '.php'], ['', ''], $real_path);
                            $route = strtolower(str_replace(['/controllers', 'Controller'], ['', ''], $namespace));
                            $namespace = 'apis' . str_replace('/', '\\', $namespace);
                            $files[$route] = $namespace;
                        }
                    }
                }
                if ($handle) {
                    closedir($handle);
                }
            }
            return $files;
        } catch (\Exception $exc) {
            throw $exc;
        }
    }
}