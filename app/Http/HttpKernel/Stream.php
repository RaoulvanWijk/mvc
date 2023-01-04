<?php

namespace App\Http\HttpKernel;

class Stream implements \Psr\Http\Message\StreamInterface
{

  /**
   * @var resource
   */
  private $resource;

  /**
   * @var array
   */
  private array $metaData;

  /**
   * Stream constructor.
   *
   * @param resource $resource
   * @param array $metaData
   */
  public function __construct($resource, array $metaData = [])
  {
    $this->resource = $resource;
    $this->metaData = stream_get_meta_data($this->resource);
  }

  /**
   * @inheritDoc
   */
  public function __toString()
  {
    if (!$this->isReadable()) {
      return '';
    }

    try {
      $this->seek(0);
      return $this->getContents();
    } catch (\Exception $e) {
      return '';
    }
  }

  /**
   * @inheritDoc
   */
  public function close()
  {
    if (is_resource($this->resource)) {
      fclose($this->resource);
    }

    $this->detach();
  }

  /**
   * @inheritDoc
   */
  public function detach()
  {
    $resource = $this->resource;
    $this->resource = null;
    $this->metaData = [];
    return $resource;
  }

  /**
   * @inheritDoc
   */
  public function getSize()
  {
    return $this->metaData['size'] ?? null;
  }

  /**
   * @inheritDoc
   */
  public function eof(): bool
  {
    return !$this->resource || feof($this->resource);
  }

  /**
   * @inheritDoc
   */
  public function tell(): int
  {
    if (!$this->resource) {
      throw new \RuntimeException('No resource available; cannot tell position');
    }

    $result = ftell($this->resource);
    if ($result === false) {
      throw new \RuntimeException('Error occurred during tell operation');
    }

    return $result;
  }

  /**
   * @inheritDoc
   */
  public function isSeekable()
  {
    return $this->metaData['seekable'] ?? false;
  }

  /**
   * @inheritDoc
   */
  public function seek($offset, $whence = SEEK_SET)
  {
    if (!$this->resource) {
      throw new \RuntimeException('No resource available; cannot seek position');
    }

    if (!$this->isSeekable()) {
      throw new \RuntimeException('Stream is not seekable');
    }

    if (fseek($this->resource, $offset, $whence) === -1) {
      throw new \RuntimeException('Error occurred during seek operation');
    }
  }

  /**
   * @inheritDoc
   */
  public function rewind()
  {
    $this->seek(0);
  }

  /**
   * @inheritDoc
   */
  public function isWritable(): bool
  {
    return $this->metaData['mode'][0] === 'x' || $this->metaData['mode'][0] === 'c' || $this->metaData['mode'][1] === 'w' || $this->metaData['mode'][2] === 'w' || $this->metaData['mode'] === "w+b";
  }

  /**
   * @inheritDoc
   */
  public function write($string): int
  {
    if (!$this->resource) {
      throw new \RuntimeException('No resource available; cannot write');
    }

    if (!$this->isWritable()) {
      throw new \RuntimeException('Stream is not writable');
    }

    $result = fwrite($this->resource, $string);
    if ($result === false) {
      throw new \RuntimeException('Error occurred during write operation');
    }

    return $result;
  }

  /**
   * @inheritDoc
   */
  public function isReadable(): bool
  {
    return $this->metaData['mode'][0] === 'r' || $this->metaData['mode'][0] === '+' || $this->metaData['mode'][1] === 'r' || $this->metaData['mode'][2] === 'r' || $this->metaData['mode'] === "w+b";
  }

  /**
   * @inheritDoc
   */
  public function read($length): string
  {
    if (!$this->resource) {
      throw new \RuntimeException('No resource available; cannot read');
    }

    if (!$this->isReadable()) {
      throw new \RuntimeException('Stream is not readable');
    }

    $result = fread($this->resource, $length);
    if ($result === false) {
      throw new \RuntimeException('Error occurred during read operation');
    }

    return $result;
  }

  /**
   * @inheritDoc
   */
  public function getContents(): string
  {
    if (!$this->isReadable()) {
      throw new \RuntimeException('Cannot read from non-readable stream');
    }

    $contents = stream_get_contents($this->resource);
    if ($contents === false) {
      throw new \RuntimeException('Error occurred during getContents operation');
    }

    return $contents;
  }

  /**
   * @inheritDoc
   */
  public function getMetadata($key = null)
  {
    if ($key === null) {
      return $this->metaData;
    }

    return $this->metaData[$key] ?? null;
  }
}