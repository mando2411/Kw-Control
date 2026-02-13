<?php

namespace App\Support;

use App\Models\User;
use Illuminate\Support\Facades\DB;

class StatementSearchCache
{
    public static function buildKey(string $prefix, array $payload): string
    {
        $normalized = self::normalize($payload);
        $encoded = json_encode($normalized, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

        return $prefix . ':' . hash('sha256', (string) $encoded);
    }

    public static function dataVersion(): string
    {
        $parts = [
            self::entityStamp('voters'),
            self::entityStamp('families'),
            self::entityStamp('committees'),
            self::entityStamp('contractors'),
            self::entityStamp('selections'),
            self::entityStamp('elections'),
            self::pivotStamp('contractor_voter'),
            self::pivotStamp('election_voter'),
        ];

        return hash('sha256', json_encode($parts, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
    }

    public static function userScopeToken(?User $user): array
    {
        if (!$user) {
            return ['guest' => true];
        }

        return [
            'id' => (int) $user->id,
            'is_admin' => (bool) $user->hasRole('Administrator'),
            'election_id' => (int) ($user->election_id ?? 0),
        ];
    }

    private static function entityStamp(string $table): array
    {
        $row = DB::table($table)
            ->selectRaw('COUNT(*) as cnt, COALESCE(MAX(id), 0) as max_id, COALESCE(MAX(updated_at), "1970-01-01 00:00:00") as max_updated_at')
            ->first();

        return [
            'table' => $table,
            'cnt' => (int) ($row->cnt ?? 0),
            'max_id' => (int) ($row->max_id ?? 0),
            'max_updated_at' => (string) ($row->max_updated_at ?? '1970-01-01 00:00:00'),
        ];
    }

    private static function pivotStamp(string $table): array
    {
        $row = DB::table($table)
            ->selectRaw('COUNT(*) as cnt, COALESCE(MAX(updated_at), "1970-01-01 00:00:00") as max_updated_at')
            ->first();

        return [
            'table' => $table,
            'cnt' => (int) ($row->cnt ?? 0),
            'max_updated_at' => (string) ($row->max_updated_at ?? '1970-01-01 00:00:00'),
        ];
    }

    private static function normalize(mixed $value): mixed
    {
        if (is_array($value)) {
            $normalized = [];
            foreach ($value as $key => $item) {
                $normalized[(string) $key] = self::normalize($item);
            }
            ksort($normalized);
            return $normalized;
        }

        if (is_object($value)) {
            return self::normalize((array) $value);
        }

        if (is_bool($value) || is_null($value) || is_numeric($value)) {
            return $value;
        }

        return trim((string) $value);
    }
}
