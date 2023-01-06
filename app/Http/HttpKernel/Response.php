<?php

namespace App\Http\HttpKernel;

use Psr\Http\Message\StreamInterface;

class Response implements \Psr\Http\Message\ResponseInterface
{

  private array $headers;
  private ?StreamInterface $body;
  private int $statusCode;

  private string $protocol;
  private string $reasonPhrase;

  public function __construct(
    int $status = 200,
    array $headers = [],
    StreamInterface $body = null,
    string $version = '1.1',
    string $reasonPhrase = ''
  ) {
    $this->statusCode = $status;
    $this->headers = $headers;
    if(is_null($body)) {
        $resource = fopen("php://temp", 'w+');
        $this->body = new Stream($resource);
    } else {
        $this->body = $body;
    }
    $this->protocol = $version;
    $this->reasonPhrase = $reasonPhrase;
  }

  /**
   * @inheritDoc
   */
  public function getProtocolVersion(): string
  {
    return $this->protocol;
  }

  /**
   * @inheritDoc
   */
  public function withProtocolVersion($version): Response
  {
    $new = clone $this;
    $new->protocol = $version;
    return $new;
  }

  /**
   * @inheritDoc
   */
  public function getHeaders(): array
  {
    return $this->headers;
  }

  /**
   * @inheritDoc
   */
  public function hasHeader($name): bool
  {
    return isset($this->headers[$name]);
  }

  /**
   * @inheritDoc
   */
  public function getHeader($name)
  {
    return isset($this->headers[$name]) ? $this->headers[$name] : [];
  }

  /**
   * @inheritDoc
   */
  public function getHeaderLine($name): string
  {
    return implode(', ', $this->getHeader($name));
  }

  /**
   * @inheritDoc
   */
  public function withHeader($name, $value): Response
  {
    $new = clone $this;
    $new->headers[$name] = (array) $value;
    return $new;
  }

  /**
   * @inheritDoc
   */
  public function withAddedHeader($name, $value): Response
  {
    $new = clone $this;
    if (isset($new->headers[$name])) {
      $new->headers[$name] = array_merge($new->headers[$name], (array) $value);
    } else {
      $new->headers[$name] = (array) $value;
    }
    return $new;
  }

  /**
   * @inheritDoc
   */
  public function withoutHeader($name): Response
  {
    $new = clone $this;
    unset($new->headers[$name]);
    return $new;
  }

  /**
   * @inheritDoc
   */
  public function getBody(): ?StreamInterface
  {
    return $this->body;
  }

  /**
   * @inheritDoc
   */
  public function withBody(StreamInterface $body): Response
  {
    $new = clone $this;
    $new->body = $body;
    return $new;
  }

  /**
   * @inheritDoc
   */
  public function getStatusCode(): int
  {
    return $this->statusCode;
  }

  /**
   * @inheritDoc
   */
  public function withStatus($code, $reasonPhrase = ''): Response
  {
    $new = clone $this;
    $new->statusCode = $code;
    $new->reasonPhrase = $reasonPhrase;
    return $new;
  }

  /**
   * @inheritDoc
   */
  public function getReasonPhrase(): ?string
  {
    return $this->reasonPhrase;
  }

  /**
   * This method is used to send the request to the server
   * @return void
   */
  public function send(): void
  {
    // Send the response status line
    \header(sprintf('HTTP/%s %s %s', $this->protocol, $this->statusCode, $this->reasonPhrase), true, $this->statusCode);

    // Send the response headers
    foreach ($this->headers as $name => $values) {
      foreach ($values as $value) {
        header("{$name}: {$value}", false);
      }
    }

    // Send the response body
    $body = $this->getBody();
    if ($body->isSeekable()) {
      $body->rewind();
    }
    while (!$body->eof()) {
      echo $body->read(1024);
    }
  }
}