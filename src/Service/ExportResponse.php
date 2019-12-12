<?php

/**
 * This file is part of the Organizer package.
 *
 * (c) Doug Harple <dharple@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Service;

use App\Entity\Box;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\CsvEncoder;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Mime\MimeTypes;

/**
 * Exporter response
 */
class ExportResponse
{

    /**
     * @var string
     */
    protected $filename;

    /**
     * @var string
     */
    protected $format = '.bin';

    /**
     * Returns the MIME type for the given format.
     */
    public function getContentType(): string
    {
        $contentType = 'application/octet-stream';

        $mimeTypes = (new MimeTypes())->getMimeTypes($this->format);
        return array_shift($mimeTypes);
    }

    /**
     * Returns a filename that can be served to the user.
     */
    public function getFilename(): string
    {
        return $this->filename;
    }

    /**
     * Returns the format (file extension).
     */
    public function getFormat(): string
    {
        return $this->format;
    }

    /**
     * Sets the data that's being exported.
     *
     * This writes out a temp file, and stores that.
     */
    public function setData(string $data): self
    {
        $this->filename = tempnam('/tmp', 'export_data_');
        file_put_contents($this->filename, $data);

        return $this;
    }

    /**
     * Sets the filename to export.
     */
    public function setFilename($filename): self
    {
        $this->filename = $filename;

        return $this;
    }

    /**
     * Sets the file format (extension, really).
     */
    public function setFormat(string $format): self
    {
        $this->format = $format;

        return $this;
    }
}
