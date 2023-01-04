<?php

namespace App\Http\HttpKernel;

use Psr\Http\Message\UriInterface;

class Uri implements UriInterface
{
  private string $scheme;
  private string $host;
  private string $path;
  private string $query;
  private string $fragment;

  public function __construct(string $scheme, string $host, string $path, string $query, string $fragment)
  {
    $this->scheme = $scheme;
    $this->host = $host;
    $this->path = $path;
    $this->query = $query;
    $this->fragment = $fragment;
  }

  /**
   * @inheritDoc
   */
  public function getScheme(): string
  {
    return $this->scheme;
  }

  /**
   * @inheritDoc
   */
  public function getAuthority(): string
  {
    return $this->host;
  }

  /**
   * @inheritDoc
   */
  public function getUserInfo(): string
  {
    return '';
  }

  /**
   * @inheritDoc
   */
  public function getHost(): string
  {
    return $this->host;
  }

  /**
   * @inheritDoc
   */
  public function getPort(): int|string|null
  {
    return '';
  }

  /**
   * @inheritDoc
   */
  public function getPath(): string
  {
    return $this->path;
  }

  /**
   * @inheritDoc
   */
  public function getQuery(): string
  {
    return $this->query;
  }

  /**
   * @inheritDoc
   */
  public function getFragment(): string
  {
    return $this->fragment;
  }

  /**
   * @inheritDoc
   */
  public function withScheme($scheme): Uri
  {
    $this->scheme = $scheme;
    return clone $this;
  }

  /**
   * @inheritDoc
   */
  public function withUserInfo($user, $password = null): Uri
  {
    return clone $this;
  }

  /**
   * @inheritDoc
   */
  public function withHost($host): Uri
  {
    $this->host = $host;
    return clone $this;
  }

  /**
   * @inheritDoc
   */
  public function withPort($port): Uri
  {
    return clone $this;
  }

  /**
   * @inheritDoc
   */
  public function withPath($path): Uri
  {
    $this->path = $path;
    return clone $this;
  }

  /**
   * @inheritDoc
   */
  public function withQuery($query): Uri
  {
    $this->query = $query;
    return clone $this;
  }

  /**
   * @inheritDoc
   */
  public function withFragment($fragment): Uri
  {
    $this->fragment = $fragment;
    return clone $this;
  }

  /**
   * @inheritDoc
   */
  public function __toString()
  {
    return $this->getScheme() . '://' . $this->getHost() . $this->getPath() . '?' . $this->getQuery() . '#' . $this->getFragment();
  }
}
{

}