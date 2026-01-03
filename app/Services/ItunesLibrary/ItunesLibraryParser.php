<?php

namespace App\Services\ItunesLibrary;

use App\Services\Logger\Logger;

/*
Itunes XML parser for PHP
Copyright (C) 2013 Conan Theobald [http://github.com/shuckster]
version: 1.5
Changes:
 * 1.5: Simplify parseDict, API changes
 * 1.4: Parse info and playlists
 * 1.3: New example, delete old/deprecated stuff
 * 1.2: Now a class, improved sort-method
 * 1.1: Type-cast integers and booleans

 */

class ItunesLibraryParser
{
    public $file_name = '';

    public $data = null;

    public $sort_field = null;

    public $sort_direction = 'ascending';

    private $channel = 'itunes_library_importer';

    public function parse(string $file)
    {
        $memoryLimit = ini_get('memory_limit'); // e.g. "512M"
        $limitValue = (int) filter_var($memoryLimit, FILTER_SANITIZE_NUMBER_INT);

        // Only check if limit is in MB
        if (str_ends_with($memoryLimit, 'M') && $limitValue < 512) {
            $msg = 'Memory limit too low: ' . $memoryLimit . '. Please set memory_limit to at least 2048M in php.ini.';
            Logger::log('error', $this->channel, $msg);
            throw new \RuntimeException($msg);
        }

        if (!is_file($file) || !$file || !is_string($file)) {
            $msg = 'Invalid file or path: ' . $file . '.';
            Logger::log('error', $this->channel, $msg);
            throw new \RuntimeException($msg);
        }

        if (!file_exists($file)) {
            $msg = 'iTunes Library Parser: XML file not found: ' . $file . '.';
            Logger::log('error', $this->channel, $msg);
            throw new \RuntimeException($msg);
        }

        libxml_use_internal_errors(true);

        $dom = new \DOMDocument;

        if (!$dom->load($file)) {
            $errors = libxml_get_errors();
            libxml_clear_errors();

            $msg = "iTunes Library Parser: Failed to parse XML file: {$file}";
            Logger::log('error', $this->channel, $msg . ' | Errors: ' . json_encode($errors));
            throw new \RuntimeException($msg);
        }

        // Get the root element <plist>
        $plist_node = $dom->documentElement;
        $first_dict_node = null;

        // First <dict> contains version-info + tracks-node
        foreach ($plist_node->childNodes as $child) {
            if ($child->nodeName === 'dict') {
                $first_dict_node = $child;
                break;
            }
        }

        $this->file_name = $file;
        $this->data = $this->parseDict($first_dict_node, null);

        $this->data['Tracks'] = (array) $this->data['Tracks'];
        // Standard: Tracks are index by Track Id
        // $track[3783]...

        // Add a seperate "Tracks by normal index"
        // $track[0]...
        // foreach ($this->data['Tracks'] as $track) {
        // $new[] = $track;
        // }
        // $this->data['TracksIndexed'] = $new;

        return $this->data;
    }

    protected function parseDict($baseNode)
    {
        $dicts = [];
        $current_key = null;
        $current_value = null;

        foreach ($baseNode->childNodes as $child) {
            $dict = null;

            switch ($child->nodeName) {
                case '#text':
                    break;

                case 'key':
                    $current_key = $child->textContent;
                    $current_value = null;
                    break;

                case 'array':
                    $current_value = $this->parseDict($child);
                    break;

                case 'dict':
                    $current_value = (object) $this->parseDict($child);
                    break;

                case 'true':
                case 'false':
                    $current_value = $child->nodeName === 'true';
                    break;

                case 'integer':
                    $current_value = (int) $child->textContent;
                    break;

                default:
                    $current_value = $child->textContent;

                    if (preg_match('/^(Music Folder|Location)$/', $current_key)) {
                        $current_value = rawurldecode(stripslashes($current_value));
                    }
            }

            if ($current_value !== null) {
                if ($baseNode->nodeName === 'array') {
                    $dicts[] = $current_value;
                } elseif ($current_key !== null) {
                    $dicts[$current_key] = $current_value;
                    $current_key = null;
                }

                $current_value = null;
            }
        }

        // Sort the tracks
        /*
        if ($this->sort_field) {
            uasort($dicts, [$this, 'sort']);
        }
        */

        return $dicts;
    }
}
