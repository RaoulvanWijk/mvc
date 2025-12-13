<?php

namespace App\Http\HttpKernel;

use Psr\Http\Message\UriInterface;

class Uri implements UriInterface
{
  private string $scheme = '';

  private string $userInfo = '';

  private string $host = '';
  private string $port = '';
  private string $path = '';
  private string $query = '';
  private string $fragment = '';

  public function __construct(string $scheme, string $host, string $port, string $path, string $query, string $fragment)
  {
    $this->scheme = $scheme;
    $this->host = $host;
    $this->port = $port;
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
    return strtolower($this->host);
  }

  /**
   * @inheritDoc
   */
  public function getPort(): ?int
  {
    return $this->port ? (int) $this->port : null;
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
  public function withScheme(string $scheme): UriInterface
  {
    $new = clone $this;
    $new->scheme = $scheme;
    return $new;
  }

  /**
   * @inheritDoc
   */
  public function withUserInfo(string $user, ?string $password = null): UriInterface
  {
    return clone $this;
  }

  /**
   * @inheritDoc
   */
  public function withHost(string $host): UriInterface
  {
    $new = clone $this;
    $new->host = $host;
    return $new;
  }

  /**
   * @inheritDoc
   */
  public function withPort(?int $port): UriInterface
  {
    $new = clone $this;
    if ($port === null) {
      $new->port = '';
    } else {
      $new->port = (string)$port;
    }
    return $new;
  }

  /**
   * @inheritDoc
   */
  public function withPath(string $path): UriInterface
  {
    $new = clone $this;
    $new->path = $path;
    return $new;
  }

  /**
   * @inheritDoc
   */
  public function withQuery(string $query): UriInterface
  {
    $new = clone $this;
    $new->query = $query;
    return $new;
  }

  /**
   * @inheritDoc
   */
  public function withFragment(string $fragment): UriInterface
  {
    $new = clone $this;
    $new->fragment = $fragment;
    return $new;
  }

  /**
   * @inheritDoc
   */
  public function __toString(): string
  {
    $uri = '';
    if ($this->getScheme() !== '') {
        $uri .= $this->getScheme() . ':';
    }
    if ($this->getAuthority() !== '' || $this->getHost() !== '') {
        $uri .= '//';
        if ($this->getUserInfo() !== '') {
            $uri .= $this->getUserInfo() . '@';
        }
        $uri .= $this->getHost();
        if ($this->getPort() !== null) {
            $uri .= ':' . $this->getPort();
        }
    }
    $path = $this->getPath();
    if ($path !== '') {
        if ($path[0] !== '/') {
            if ($this->getAuthority() !== '' || $this->getHost() !== '') {
                $path = '/' . $path;
            }
        }
    }
    $uri .= $path;
    if ($this->getQuery() !== '') {
        $uri .= '?' . $this->getQuery();
    }
    if ($this->getFragment() !== '') {
        $uri .= '#' . $this->getFragment();
    }

    return $uri;
  }
}
