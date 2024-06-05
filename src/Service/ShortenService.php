<?php

namespace Drupal\shorten\Service;

use GuzzleHttp\ClientInterface;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Database\Connection;
use Psr\Log\LoggerInterface;
use Drupal\Core\Logger\LoggerChannelFactoryInterface;
use GuzzleHttp\Exception\RequestException;

/**
 * Class ShortenService.
 */
class ShortenService {

  protected $httpClient;
  protected $configFactory;
  protected $database;
  protected $logger;

  /**
   * Constructs a new ShortenService object.
   */
  public function __construct(ClientInterface $http_client, ConfigFactoryInterface $config_factory, Connection $database, LoggerChannelFactoryInterface $logger_factory) {
    $this->httpClient = $http_client;
    $this->configFactory = $config_factory;
    $this->database = $database;
    $this->logger = $logger_factory->get('shorten');
  }

  /**
   * Shorten a URL using the specified service.
   */
  public function shorten($url, $service) {
    $config = $this->configFactory->get('shorten.settings');

    switch ($service) {
      case 'bitly':
        $bitly_token = $config->get('bitly_api_key');
        $api_url = "https://api-ssl.bitly.com/v4/shorten";
        $options = [
          'json' => ['long_url' => $url],
          'headers' => [
            'Authorization' => "Bearer $bitly_token",
            'Content-Type' => 'application/json',
          ],
        ];
        $response = $this->httpClient->post($api_url, $options);
        $data = json_decode($response->getBody());
        $shortened_url = $data->link;
        break;

      case 'tinyurl':
        $api_url = "https://api.tinyurl.com/create";
        $api_key = $config->get('tinyurl_api_key');
        $options = [
          'json' => [
            'url' => $url,
            'domain' => 'tiny.one'
          ],
          'headers' => [
            'Authorization' => "Bearer $api_key",
            'Content-Type' => 'application/json',
          ],
        ];
        $response = $this->httpClient->post($api_url, $options);
        $data = json_decode($response->getBody());
        $shortened_url = $data->data->tiny_url;
        break;

      case 'isgd':
        $api_url = "https://is.gd/create.php";
        $options = [
          'query' => [
            'format' => 'json',
            'url' => $url
          ],
        ];
        $response = $this->httpClient->get($api_url, $options);
        $data = json_decode($response->getBody());
        $shortened_url = $data->shorturl;
        break;

      case 'googl':
        $api_key = $config->get('googl_api_key');
        $api_url = "https://www.googleapis.com/urlshortener/v1/url?key=$api_key";
        $options = [
          'json' => ['longUrl' => $url],
        ];
        $response = $this->httpClient->post($api_url, $options);
        $data = json_decode($response->getBody());
        $shortened_url = $data->id;
        break;

      case 'cligs':
        $api_url = "http://cli.gs/api/v1/cligs/create";
        $api_key = $config->get('cligs_api_key');
        $options = [
          'query' => [
            'appid' => $api_key,
            'url' => $url
          ],
        ];
        $response = $this->httpClient->get($api_url, $options);
        $data = json_decode($response->getBody());
        $shortened_url = $data->short_url;
        break;

      case 'fwd4me':
        $api_url = "http://fwd4.me/api";
        $api_key = $config->get('fwd4me_api_key');
        $options = [
          'query' => [
            'key' => $api_key,
            'url' => $url
          ],
        ];
        $response = $this->httpClient->get($api_url, $options);
        $data = json_decode($response->getBody());
        $shortened_url = $data->shorturl;
        break;

      case 'migreme':
        $api_url = "http://migre.me/api.txt";
        $options = [
          'query' => [
            'url' => $url
          ],
        ];
        $response = $this->httpClient->get($api_url, $options);
        $shortened_url = (string) $response->getBody();
        break;

      case 'peew':
        $api_url = "http://peew.pw/api";
        $options = [
          'query' => [
            'url' => $url
          ],
        ];
        $response = $this->httpClient->get($api_url, $options);
        $data = json_decode($response->getBody());
        $shortened_url = $data->shorturl;
        break;

      case 'qr':
        $api_url = "http://qr.cx/api";
        $options = [
          'query' => [
            'url' => $url
          ],
        ];
        $response = $this->httpClient->get($api_url, $options);
        $data = json_decode($response->getBody());
        $shortened_url = $data->shorturl;
        break;

      case 'redir':
        $api_url = "http://redir.ec/api";
        $api_key = $config->get('redir_api_key');
        $options = [
          'query' => [
            'key' => $api_key,
            'url' => $url
          ],
        ];
        $response = $this->httpClient->get($api_url, $options);
        $data = json_decode($response->getBody());
        $shortened_url = $data->shorturl;
        break;

      case 'ri':
        $api_url = "http://ri.ms/api";
        $options = [
          'query' => [
            'url' => $url
          ],
        ];
        $response = $this->httpClient->get($api_url, $options);
        $data = json_decode($response->getBody());
        $shortened_url = $data->shorturl;
        break;

      case 'dubco':
        $dubco_token = $config->get('dubco_api_key');
        $dubco_workspace_id = $config->get('dubco_workspace_id');
        $api_url = "https://api.dub.co/links?workspaceId={$dubco_workspace_id}";
        $options = [
            'json' => ['url' => $url],
            'headers' => [
                'Authorization' => "Bearer $dubco_token",
                'Content-Type' => 'application/json',
            ],
        ];
        try {
            $response = $this->httpClient->post($api_url, $options);
            $data = json_decode($response->getBody(), true);

            // Log the full API response for debugging
            $this->logger->info('dub.co API full response: @response', ['@response' => json_encode($data)]);

            // Extract the short URL from the response
            if (isset($data['shortLink'])) {
                $shortened_url = $data['shortLink'];
            } else {
                // Log an error and throw an exception if shortLink is not found
                $this->logger->error('shortLink not found in dub.co API response.');
                throw new \Exception('shortLink not found in dub.co API response.');
            }
        } catch (RequestException $e) {
            $this->logger->error('Error shortening URL with dub.co: @message', ['@message' => $e->getMessage()]);
            throw $e;
        }
        break;

      default:
        throw new \Exception('Unsupported service: ' . $service);
    }

    // Log the shortened URL creation.
    $this->logger->info('Shortened URL created: @original => @shortened', [
      '@original' => $url,
      '@shortened' => $shortened_url,
    ]);

    // Insert the shortened URL record into the database.
    if (!empty($shortened_url)) {
        $this->database->insert('shorten_urls')
          ->fields([
            'original_url' => $url,
            'short_url' => $shortened_url,
            'service' => $service,
            'created' => REQUEST_TIME,
          ])
          ->execute();
    } else {
        $this->logger->error('Cannot insert into database, short_url is null.');
        throw new \Exception('Cannot insert into database, short_url is null.');
    }

    return $shortened_url;
  }
}
