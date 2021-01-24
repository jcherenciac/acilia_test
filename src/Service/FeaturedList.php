<?php

namespace App\Service;


use App\Entity\Product;
use App\Repository\ProductRepository;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class FeaturedList
{
    /**
     * @var ProductRepository
     */
    private $productRepository;
    /**
     * @var HttpClientInterface
     */
    private $client;

    /**
     * FeaturedList constructor.
     * @param ProductRepository $productRepository
     * @param HttpClientInterface $client
     */
    public function __construct(ProductRepository $productRepository, HttpClientInterface $client)
    {
        $this->productRepository = $productRepository;
        $this->client = $client;
    }

    /**
     * @param $currency
     * @return array
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function getFeaturedList($currency)
    {
        $response = $this->productRepository->getFeatured();

        if ($currency) {
            $base = $currency === Product::CURRENCY_EUR ? Product::CURRENCY_USD : Product::CURRENCY_EUR;
            try {
                $exchangeRate = $this->getExchange($base, $currency);
                if ($exchangeRate) {
                    foreach ($response as $key => $item) {
                        $itemCurrency = !empty($item['currency']) ? $item['currency'] : null;
                        if ($itemCurrency && $itemCurrency !== $currency) {
                            $response[$key]['price'] = round($item['price'] * $exchangeRate['rates'][$currency], 2);
                            $response[$key]['currency'] = $currency;
                        }
                    }
                }

            } catch (TransportExceptionInterface $e) {
                throw $e;
            } catch (ClientExceptionInterface $e) {
                throw $e;
            } catch (RedirectionExceptionInterface $e) {
                throw $e;
            } catch (ServerExceptionInterface $e) {
                throw $e;
            }


        }
        return $response;
    }

    /**
     * @param null $base
     * @param null $target
     * @return string|null
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function getExchange($base = null, $target = null)
    {
        $baseApiUrl = 'https://api.exchangeratesapi.io/latest';
        $url = !empty($base) && !empty($target) ? $baseApiUrl . "?base=" . $base . "&symbols=" . $target : $baseApiUrl;
        try {
            return json_decode($this->client->request('GET', $url)->getContent(), true);
        } catch (TransportExceptionInterface $e) {
            throw $e;
        } catch (ClientExceptionInterface $e) {
            throw $e;
        } catch (RedirectionExceptionInterface $e) {
            throw $e;
        } catch (ServerExceptionInterface $e) {
            throw $e;
        }

    }

}