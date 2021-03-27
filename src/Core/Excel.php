<?php

namespace GWM\Core;

/**
 * Class Excel
 *
 * \x50\x4b\x03\x04 File Signature.
 *
 * @internal
 * @TODO: Use binary instead of ZipArchive.
 * @package GWM\Core
 * @version 1.0.0
 */
class Excel
{
    public array $worksheets, $strings, $data;
    private string $name;

    public function __construct($name)
    {
        $this->worksheets = [];
        $this->strings = [];
        $this->data = [];
        $this->name = $name;
    }

    private function _rmdir($name)
    {
        $result = 1;

        if (is_dir($name)) {
            $dir = new \RecursiveDirectoryIterator($name, \RecursiveDirectoryIterator::SKIP_DOTS);
            foreach (new \RecursiveIteratorIterator($dir, \RecursiveIteratorIterator::CHILD_FIRST) as $filename => $file) {
                if (is_file($filename))
                    $result &= unlink($filename);
                else
                    $result &= rmdir($filename);
            }
            $result &= rmdir($name);
        }

        return $result;
    }

    public function load()
    {
        $za = new \ZipArchive;
        $za->open(GWM['DIR_PUBLIC'] . "/$this->name.xlsx");

        $dir = GWM['DIR_TMP'] . '/gwm';
        if (!is_dir($dir)) {
            mkdir($dir);
        }

        $final = $dir . '/' . md5($this->name);

        if ($za->extractTo($final) == true) {
            $contents = file_get_contents($final . '/[Content_Types].xml');

            $xml = \simplexml_load_string($contents);

            foreach ($xml->Override ?? [] as $override) {
                foreach ($override->attributes() as $attr) {
                    $exp = explode('/', $attr);
                    if (in_array('worksheets', $exp)) {
                        $last = $exp[sizeof($exp) - 1];
                        $this->worksheets[] = $last;
                    }
                }
            }

            $sharedStrings = file_get_contents($final . '/xl/sharedStrings.xml');

            $xml = \simplexml_load_string($sharedStrings);

            foreach ($xml->si ?? [] as $element) {
                $this->strings[] = $element->saveXML();
            }

            foreach ($this->worksheets as $worksheet) {
                $contents = file_get_contents($final . '/xl/worksheets/' . $worksheet);

                $xml = \simplexml_load_string($contents);

                echo '<pre>';

                foreach ($xml->sheetData->row ?? [] as $row) {
                    foreach ($row as $element) {
                        $value = (int)$element->v ?? 0;

                        if ($value > 0 && $value < sizeof($this->strings)) {
                            $this->data[] = $this->strings[$value];
                        } else if ($value > 0) {
                            $this->data[] = $value;
                        }
                    }
                }

                echo '</pre>';
            }
        }
        return $za->close() && is_dir($final) ? $this->_rmdir($final) : false;
    }
}