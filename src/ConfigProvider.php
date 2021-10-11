<?php

declare(strict_types=1);
/**
 * This file is part of Hyperf.
 *
 * @link     https://www.hyperf.io
 * @document https://doc.hyperf.io
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */
namespace Lysice\HyperfUserSettings;

class ConfigProvider
{
    public function __invoke(): array
    {
        return [
            'dependencies' => [
            ],
            'commands' => [
            ],
            'annotations' => [
                'scan' => [
                    'paths' => [
                        __DIR__,
                    ],
                ],
            ],
            'publish' => [
                [
                    'id' => 'config',
                    'description' => 'The config for hyperf-user-settings.',
                    'source' => __DIR__ . '/../publish/user-setting.php',
                    'destination' => BASE_PATH . '/config/autoload/user-setting.php',
                ],
                [
                    'id' => 'user-settings-migration',
                    'description' => 'The migration of database to migrate.',
                    'source' => __DIR__ . '/../migrations/2021_09_27_122211_add_settings_to_users_table.php',
                    'destination' => BASE_PATH . '/migrations/2021_09_27_122211_add_settings_to_users_table.php',
                ],
            ],
        ];
    }
}
