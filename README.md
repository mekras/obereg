# Obereg

PHP application fault tolerance library


## Examples

### HTTP

```php

/** @var \Http\Client\HttpClient $httpClient */
$gw = new HttpGateway($httpClient);

/** @var \Psr\Http\Message\RequestInterface $request */
$response = $gw->sendRequest($request);
```

* **Шлюз (Gateway)** — объект, через который идёт обмен данными между двумя системами. Шлюз
  обеспечивает перехват и обработку ошибок.
* **Очередь (Queue)** — очередь исходящих данных, которые не удалось отправить.
* **Кэш (Cache)** — кэш входящих данных.
* **Политика (Policy)** — набор правил, определяющий поведение очереди или кэша.
* **Хранилище (Storage)** — низкоуровневое хранилище данных очередей и кэшей.
