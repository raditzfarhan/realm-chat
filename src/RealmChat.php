<?php

namespace RaditzFarhan\RealmChat;

use Illuminate\Support\Facades\Http;
use RaditzFarhan\RealmChat\Exceptions\APIException;
use RaditzFarhan\RealmChat\Exceptions\MissingParameterException;

class RealmChat
{
    private string $baseUrl = 'https://client.realm.chat/api/v1';

    private ?string $route;

    private string $endpoint;

    private string $method;

    private ?string $action;

    private ?string $deviceId;

    private array $payload = [];

    public function __construct(public string $apiKey)
    {
    }

    public function addDevice(string $name): array
    {
        $this->setMethod('post');
        $this->setAction('add-device');
        $this->setRoute('/device');
        $this->setPayload([
            'name' => $name,
        ]);

        return $this->sendRequest();
    }

    public function sendMessage(string $number, string $message, ?string $fileUrl = null, ?string $fileName = null): array
    {
        $this->checkDeviceIdExists();
        $this->setMethod('post');
        $this->setAction('send-message');

        $payload = [
            'number' => $number,
            'message' => $message,
            'type' => $fileUrl ? 'image' : 'text',
        ];

        if ($fileUrl && $fileName) {
            $payload = array_merge($payload, [
                'fileUrl' => $fileUrl,
                'fileName' => $fileName,
            ]);
        }

        $this->setPayload($payload);

        return $this->sendRequest();
    }

    public function sendButtonMessage(string $number, array $buttons, ?string $message, ?string $fileUrl = null): array
    {
        $this->checkDeviceIdExists();
        $this->setMethod('post');
        $this->setAction('send-button-message');

        $payload = [
            'number' => $number,
            'buttons' => $buttons,
        ];

        if ($message) {
            $payload = array_merge($payload, [
                'message' => $message
            ]);
        }

        if ($fileUrl) {
            $payload = array_merge($payload, [
                'fileUrl' => $fileUrl
            ]);
        }

        $this->setPayload($payload);

        return $this->sendRequest();
    }

    public function sendTemplateMessage(string $number, array $templates, string $message, ?string $fileUrl = null): array
    {
        $this->checkDeviceIdExists();
        $this->setMethod('post');
        $this->setAction('send-button-message');

        $payload = [
            'number' => $number,
            'message' => $message,
            'templates' => $templates,
        ];

        if ($fileUrl) {
            $payload = array_merge($payload, [
                'fileUrl' => $fileUrl
            ]);
        }

        $this->setPayload($payload);

        return $this->sendRequest();
    }

    public function recentChat(): array
    {
        $this->checkDeviceIdExists();
        $this->setMethod('post');
        $this->setAction('recent-chats');

        return $this->sendRequest();
    }

    public function getContact(): array
    {
        $this->checkDeviceIdExists();
        $this->setMethod('post');
        $this->setAction('get-contacts');

        return $this->sendRequest();
    }

    public function checkNumber(string $number): array
    {
        $this->checkDeviceIdExists();
        $this->setMethod('post');
        $this->setAction('check-number');

        $this->setPayload([
            'number' => $number,
        ]);

        return $this->sendRequest();
    }

    private function sendRequest(): array
    {
        $payload = $this->payload;
        $payload = array_merge($payload, ['key' => $this->apiKey]);

        if (isset($this->action)) {
            $payload = array_merge($payload, ['action' => $this->action]);
        }

        if (isset($this->deviceId)) {
            $payload = array_merge($payload, ['device' => $this->deviceId]);
        }

        // dd($this->getEndpoint(), $payload);
        $request = Http::asForm()->acceptJson();

        // enable only for development
        $request->withoutVerifying();

        $response = $request->{$this->method}($this->getEndpoint(), $payload);

        $response->throw(function ($response, $e) {
            throw new APIException($e->getMessage());
        });


        $result = $response->json();

        if (isset($result['result']) && $result['result'] === false) {
            if (isset($result['message'])) {
                $errorMessage = json_encode($result['message']);
            } else {
                $errorMessage = 'API return false result.';
            }

            throw new APIException('API error: ' . $errorMessage);
        }

        // reset values
        $this->setRoute(null);
        $this->setPayload([]);

        return isset($result['data']) ? $result['data'] : $result;
    }


    public function setDeviceId(string $deviceId): self
    {
        $this->deviceId = $deviceId;

        return $this;
    }

    private function setAction(string $action): self
    {
        $this->action = $action;

        return $this;
    }

    private function setMethod(string $method): self
    {
        $this->method = $method;

        return $this;
    }

    private function setPayload(array $payload): self
    {
        $this->payload = $payload;

        return $this;
    }

    private function setRoute(string|null $route): self
    {
        $this->route = $route;

        return $this;
    }

    private function setEndpoint(string $endpoint): self
    {
        $this->endpoint = $endpoint;

        return $this;
    }

    private function getEndpoint(): string
    {
        if (isset($this->route)) {
            $this->setEndpoint($this->baseUrl . $this->route);
        } else {
            $this->setEndpoint($this->baseUrl);
        }

        return $this->endpoint;
    }

    private function checkDeviceIdExists()
    {
        if (!isset($this->deviceId)) {
            throw new MissingParameterException('Missing parameter(s): deviceId');
        }

        if (isset($this->deviceId) && ($this->deviceId == '' || $this->deviceId == null)) {
            throw new MissingParameterException('Missing parameter(s): deviceId');
        }
    }
}
