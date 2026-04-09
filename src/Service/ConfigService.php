<?php

namespace Golpilolz\DBConfigs\Service;

use Golpilolz\DBConfigs\Repository\SiteVariableRepository;

readonly class ConfigService
{
    public function __construct(private SiteVariableRepository $repository)
    {
    }

    /**
     * Get raw string value for a key. Returns empty string when not set.
     */
    public function get(string $key): string
    {
        $variable = $this->repository->getValue($key);

        return (string) $variable->getValue();
    }

    /**
     * @param array<string, string> $fallback
     * @return array<string, string>
     */
    public function getJson(string $key, array $fallback = []): array
    {
        $raw = $this->get($key);

        if ($raw === '') {
            return $fallback;
        }

        /** @var array<string, string>|null $decoded */
        $decoded = json_decode($raw, true);

        return is_array($decoded) ? $decoded : $fallback;
    }

    public function getValueFromJson(string $key, string $subKey, string $fallback = ""): string
    {
        $json = $this->getJson($key);

        if (!isset($json[$subKey])) {
            return $fallback;
        }

        return $json[$subKey];
    }

    public function set(string $key, string $value): void
    {
        $this->repository->save($key, $value);
    }


    /**
     * @throws \JsonException
     * @param array<string, string> $data
     */
    public function setJson(string $key, array $data): void
    {
        $this->set($key, json_encode($data, JSON_THROW_ON_ERROR));
    }

    /**
     * Return all variables as name => value
     *
     * @return array<string, string>
     */
    public function all(): array
    {
        $items = $this->repository->findAll();
        $out = [];

        foreach ($items as $item) {
            $out[$item->getName()] = (string) $item->getValue();
        }

        return $out;
    }
}

