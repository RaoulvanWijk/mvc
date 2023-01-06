<?php

namespace App\Http\HttpKernel;

use Psr\Http\Message\UriInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamInterface;

class Request implements ServerRequestInterface
{
  /**
   * @var string
   */
  private string $requestTarget;

  /**
   * @var string
   */
  private string $method;

  /**
   * @var UriInterface
   */
  private UriInterface $uri;

  /**
   * @var string
   */
  private string $protocolVersion;

  /**
   * @var array
   */
  private array $headers;

  /**
   * @var StreamInterface
   */
  private StreamInterface $body;

  /**
   * @var array
   */
  private array $serverParams;
    
  /**
   * @var array
   */
  private array $cookieParams;

  /**
   * @var array
   */
  private array $queryParams;

  /**
   * @var array
   */
  private array $uploadedFiles;

  /**
   * @var array
   */
  private array $attributes;

  /**
   * @var array
   */
  private array $parsedBody;

  /**
   * Request constructor.
   *
   * @param string $requestTarget
   * @param string $method
   * @param UriInterface $uri
   * @param array $headers
   * @param StreamInterface|null $body
   * @param string $protocolVersion
   * @param array $serverParams
   * @param array $cookieParams
   * @param array $queryParams
   * @param array $uploadedFiles
   * @param array $attributes
   * @param array $parsedBody
   */
  public function __construct(
    string $requestTarget,
    string $method,
    UriInterface $uri,
    array $headers = [],
    StreamInterface $body = null,
    string $protocolVersion = '1.1',
    array $serverParams = [],
    array $cookieParams = [],
    array $queryParams = [],
    array $uploadedFiles = [],
    array $attributes = [],
    array $parsedBody = []
) {
    $this->requestTarget = $requestTarget;
    $this->method = $method;
    $this->uri = $uri;
    $this->headers = $headers;
    $this->body = $body;
    $this->protocolVersion = $protocolVersion;
    $this->serverParams = $serverParams;
    $this->cookieParams = $cookieParams;
    $this->queryParams = $queryParams;
    $this->uploadedFiles = $uploadedFiles;
    $this->attributes = $attributes;
    $this->parsedBody = $parsedBody;
}

  /**
   * @inheritDoc
   */
  public function getRequestTarget(): string
  {
    if ($this->requestTarget !== null) {
      return $this->requestTarget;
    }

    $target = $this->uri->getPath();
    if ($target === '') {
      $target = '/';
    }
    if ($this->uri->getQuery() !== '') {
      $target .= '?' . $this->uri->getQuery();
    }

    return $target;
  }

  /**
   * @inheritDoc
   */
  public function withRequestTarget($requestTarget): Request
  {
    $new = clone $this;
    $new->requestTarget = $requestTarget;
    return $new;
  }

  /**
   * @inheritDoc
   */
  public function getMethod(): string
  {
    return $this->method;
  }

  /**
   * @inheritDoc
   */
  public function withMethod($method): Request
  {
    $new = clone $this;
    $new->method = $method;
    return $new;
  }

  /**
   * @inheritDoc
   */
  public function getUri(): UriInterface
  {
    return $this->uri;
  }

  /**
   * @inheritDoc
   */
  public function withUri(UriInterface $uri, $preserveHost = false): Request
  {
    $new = clone $this;
    $new->uri = $uri;
    return $new;
  }

  /**
   * @inheritDoc
   */
  public function getProtocolVersion(): string
  {
    return $this->protocolVersion;
  }

  /**
   * @inheritDoc
   */
  public function withProtocolVersion($version): Request
  {
    $new = clone $this;
    $new->protocolVersion = $version;
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
    return array_key_exists($name, $this->headers);
  }

  /**
   * @inheritDoc
   */
  public function getHeader($name)
  {
    return $this->headers[$name] ?? null;
  }

  /**
   * @inheritDoc
   */
  public function getHeaderLine($name): string
  {
    return implode(',', $this->getHeader($name));
  }

  /**
   * @inheritDoc
   */
  public function withHeader($name, $value): Request
  {
    $new = clone $this;
    $new->headers[$name] = $value;
    return $new;
  }

  /**
   * @inheritDoc
   */
  public function withAddedHeader($name, $value): Request
  {
    $new = clone $this;
    $new->headers[$name][] = $value;
    return $new;
  }

  /**
   * @inheritDoc
   */
  public function withoutHeader($name): Request
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
  public function withBody($body): Request
  {
    $new = clone $this;
    $new->body = $body;
    return $new;
  }

  /**
   * @inheritDoc
   */
  public function getServerParams(): array
  {
    return $this->serverParams;
  }

  /**
   * @inheritDoc
   */
  public function getCookieParams()
  {
    return $this->cookies;
  }

  /**
   * @inheritDoc
   */
  public function withCookieParams(array $cookies): ServerRequestInterface|Request
  {
    $new = clone $this;
    $new->cookies = $cookies;

    return $new;
  }

  /**
   * @inheritDoc
   */
  public function getQueryParams(): array
  {
    return $this->queryParams;
  }

  /**
   * @inheritDoc
   */
  public function withQueryParams(array $query): ServerRequestInterface|Request
  {
    $new = clone $this;
    $new->queryParams = $query;

    return $new;
  }

  /**
   * @inheritDoc
   */
  public function getUploadedFiles(): array
  {
    return $this->uploadedFiles;
  }

  /**
   * @inheritDoc
   */
  public function withUploadedFiles(array $uploadedFiles): ServerRequestInterface|Request
  {
    $new = clone $this;
    $new->uploadedFiles = $uploadedFiles;

    return $new;
  }

  /**
   * @inheritDoc
   */
  public function getParsedBody(): object|array|null
  {
    return $this->parsedBody;
  }

  /**
   * @inheritDoc
   */
  public function withParsedBody($data): ServerRequestInterface|Request
  {
    $new = clone $this;
    $new->parsedBody = $data;

    return $new;
  }

  /**
   * @inheritDoc
   */
  public function getAttributes(): array
  {
    return $this->attributes;
  }

  /**
   * @inheritDoc
   */
  public function getAttribute($name, $default = null)
  {
    return $this->attributes[$name] ?? $default;
  }

  /**
   * @inheritDoc
   */
  public function withAttribute($name, $value): ServerRequestInterface|Request
  {
    $new = clone $this;
    $new->attributes[$name] = $value;

    return $new;
  }

  /**
   * @inheritDoc
   */
  public function withoutAttribute($name): ServerRequestInterface|Request
  {
    $new = clone $this;
    unset($new->attributes[$name]);

    return $new;
  }

  /**
   * This function will capture and return A Request class
   * With the current values of $_SERVER variables
   * @param array $attributes
   * @param $parsedBody
   * @return Request
   */
  public static function capture(array $attributes = []): Request
  {
    $parsedUrl = parse_url($_SERVER["REQUEST_URI"]);
    return new static(
      $_SERVER["REQUEST_URI"],
      $_SERVER['REQUEST_METHOD'],
      new Uri(
        $_SERVER['REQUEST_SCHEME'] ?? 'http',
        $_SERVER['SERVER_NAME'] ?? "localhost",
          $parsedUrl["path"] ?? '/',
        $parsedUrl["query"] ?? '',
        $parsedUrl["fragment"] ?? ''
      ),
      self::parseHeaders($_SERVER),
      new Stream(fopen('php://input', 'r+')),
      isset($_SERVER['SERVER_PROTOCOL']) ? str_replace('HTTP/', '', $_SERVER['SERVER_PROTOCOL']) : '1.1',
      $_SERVER,
      $_COOKIE,
      $_GET,
      self::parseUploadedFiles($_FILES),
      $attributes,
      $_POST
    );
  }

  private static function parseHeaders(array $server): array
  {
    $headers = [];
    foreach ($server as $key => $value) {
      if (str_starts_with($key, 'HTTP_')) {
        $name = str_replace('_', '-', strtolower(substr($key, 5)));
        $headers[$name] = $value;
      } elseif (str_starts_with($key, 'CONTENT_')) {
        $name = 'content-' . strtolower(substr($key, 8));
        $headers[$name] = $value;
      }
    }

    return $headers;
  }

  private static function parseUploadedFiles(array $files): array
  {
    $uploadedFiles = [];
    foreach ($files as $key => $value) {
      $uploadedFiles[$key] = self::parseUploadedFile($value);
    }

    return $uploadedFiles;
  }

  private static function parseUploadedFile(array $file): array|UploadedFile
  {
    if (is_array($file['tmp_name'])) {
      $uploadedFiles = [];
      foreach ($file['tmp_name'] as $key => $value) {
        $uploadedFiles[$key] = new UploadedFile(
          $file['tmp_name'][$key],
          $file['size'][$key],
          $file['error'][$key],
          $file['name'][$key],
          $file['type'][$key]
        );
      }

      return $uploadedFiles;
    }

    return new UploadedFile(
      $file['tmp_name'],
      $file['size'],
      $file['error'],
      $file['name'],
      $file['type']
    );
  }
}