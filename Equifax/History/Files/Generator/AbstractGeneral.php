<?php

namespace Equifax\History\Files\Generator;

use \Equifax\Xml\Generator\Generator;
use \Equifax\Core\Config;

/**
 * Класс AbstractGeneral
 * @version 0.0.1
 * @package Equifax\History\Files\Generator\AbstractGeneral
 * @generated Зорин Алексей, please DO NOT EDIT!
 * @author Зорин Алексей <zorinalexey59292@gmail.com>
 * @copyright 2022 разработчик Зорин Алексей Евгеньевич.
 */
abstract class AbstractGeneral
{

    protected ?Generator $xml = false;
    protected ?Config $config = false;
    protected ?string $fileName = null;

    public function __construct(Config $config)
    {
        $this->config = $config;
        $this->fileName = $this->getFileName();
        $this->xml = new Generator($this->fileName);
        $this->xml->startDocument()->startElement('fch', ['version' => '4.0']);
    }

    private function setHead()
    {
        $date = $this->xml->dateFormat(date('d.m.Y'));
        $this->xml->startElement('head')
            ->addElement('source_inn', $this->config->inn)
            ->addElement('source_ogrn', $this->config->ogrn)
            ->addElement('date', $date)
            ->addElement('file_reg_date', $date)
            ->addElement('file_reg_num', $this->fileName);
        if (isset($this->config->prev_file)) {
            $this->xml->startElement('prev_file')
                ->addElement('file_reg_date', $this->config->prev_file->file_reg_date)
                ->addElement('file_reg_num', $this->config->prev_file->file_reg_num)
                ->closeElement();
        }
        $this->xml->closeElement();
    }

    public function get(): string
    {
        return $this->xml->closeElement()->get();
    }

    protected function getFileName(): string
    {
        $dir = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'xml' . DIRECTORY_SEPARATOR;
        if ( ! is_dir($dir)) {
            mkdir($dir, 0777, true);
        }
        $count = 1;
        foreach (scandir($dir) as $file) {
            if (is_file($dir . $file)) {
                $count ++;
            }
        }
        return $this->config->partnerid . '_FCN_' . date('Ymd') . '_' . $count . '.XML';
    }

}
