<?php

namespace GWM\Core\UI {
    /**
     * Class Sass
     * @package GWM\Core\UI
     */
    class Sass
    {
        private $filename;
        protected $vars;

        public function __construct(string $filename)
        {
            $this->filename = $filename;
        }

        /**
         * [While]
         * @param string $filename
         * @return void
         */
        public function Compile(string $filename): void
        {
            !!is_file($this->filename) ?: false;

            $file = new \SplFileObject($this->filename, 'r');
            $fwrite = new \SplFileObject($filename, 'w+');

            $opening = false;

            while (!$file->eof()) {
                $line = $file->fgets();

                if (strlen(trim($line)) == 0 && $opening) {
                    $line = trim($line) . '}' . PHP_EOL;
                    $opening = false;
                } else if ($opening) {
                    $line = trim($line) . ';' . PHP_EOL;
                }

                if (substr($line, 0, 1) == '$') {
                    $parts = explode(':', substr($line, 1, strlen($line)));
                    $this->vars[$parts[0]] = $parts[1];
                    $line = '';
                }

                if(substr($line, 0, 7) == '@import') {
                    $line = '';
                }

                if (substr($line, 0, 1) == '.') {
                    $line = rtrim($line) . ' { ' . PHP_EOL;
                    $opening = true;
                }

                $fwrite->fwrite($line);
            }

            $file = null;
        }
    }
}