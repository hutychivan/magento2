<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Setup\Model;

use Magento\Framework\ObjectManager\DefinitionFactory;
use Magento\Framework\Setup\ConfigOptionsInterface;
use Magento\Framework\Setup\Option\SelectConfigOption;
use Magento\Framework\Setup\Option\TextConfigOption;
use Magento\Framework\App\DeploymentConfig;

/**
 * Deployment configuration options needed for Setup application
 */
class ConfigOptions implements ConfigOptionsInterface
{
    /**#@+
     * Path to the values in the deployment config
     */
    const CONFIG_PATH_INSTALL_DATE = 'install/date';
    /**#@-*/

    /**#@+
     * Input keys for the options
     */
    const INPUT_KEY_CRYPT_KEY = 'key';
    const INPUT_KEY_SESSION_SAVE = 'session_save';
    const INPUT_KEY_DEFINITION_FORMAT = 'definition_format';
    const INPUT_KEY_DB_HOST = 'db_host';
    const INPUT_KEY_DB_NAME = 'db_name';
    const INPUT_KEY_DB_USER = 'db_user';
    const INPUT_KEY_DB_PASS = 'db_pass';
    const INPUT_KEY_DB_PREFIX = 'db_prefix';
    const INPUT_KEY_DB_MODEL = 'db_model';
    const INPUT_KEY_DB_INIT_STATEMENTS = 'db_init_statements';
    const INPUT_KEY_ACTIVE = 'active';
    const INPUT_KEY_RESOURCE = 'resource';
    /**#@-*/

    /**#@+
     * Values for session_save
     */
    const SESSION_SAVE_FILES = 'files';
    const SESSION_SAVE_DB = 'db';
    /**#@-*/

    /**
     * Generate config data for individual segments
     *
     * @var ConfigDataGenerator
     */
    private $configDataGenerator;

    /**
     * Constructor
     *
     * @param ConfigDataGenerator $configDataGenerator
     */
    public function __construct(ConfigDataGenerator $configDataGenerator)
    {
        $this->configDataGenerator = $configDataGenerator;
    }

    /**
     * {@inheritdoc}
     */
    public function getOptions()
    {
        return [
            new TextConfigOption(
                self::INPUT_KEY_CRYPT_KEY,
                TextConfigOption::FRONTEND_WIZARD_TEXT,
                'Encryption key'
            ),
            new SelectConfigOption(
                self::INPUT_KEY_SESSION_SAVE,
                SelectConfigOption::FRONTEND_WIZARD_SELECT,
                [self::SESSION_SAVE_FILES, self::SESSION_SAVE_DB],
                'Session save location',
                self::SESSION_SAVE_FILES
            ),
            new SelectConfigOption(
                self::INPUT_KEY_DEFINITION_FORMAT,
                SelectConfigOption::FRONTEND_WIZARD_SELECT,
                DefinitionFactory::getSupportedFormats(),
                'Type of definitions used by Object Manager'
            ),
            new TextConfigOption(
                self::INPUT_KEY_DB_HOST,
                TextConfigOption::FRONTEND_WIZARD_TEXT,
                'Database server host'
            ),
            new TextConfigOption(
                self::INPUT_KEY_DB_NAME,
                TextConfigOption::FRONTEND_WIZARD_TEXT,
                'Database name'
            ),
            new TextConfigOption(
                self::INPUT_KEY_DB_USER,
                TextConfigOption::FRONTEND_WIZARD_TEXT,
                'Database server username'
            ),
            new TextConfigOption(
                self::INPUT_KEY_DB_PASS,
                TextConfigOption::FRONTEND_WIZARD_PASSWORD,
                'Database server password'
            ),
            new TextConfigOption(
                self::INPUT_KEY_DB_PREFIX,
                TextConfigOption::FRONTEND_WIZARD_TEXT,
                'Database table prefix'
            ),
            new TextConfigOption(
                self::INPUT_KEY_DB_MODEL,
                TextConfigOption::FRONTEND_WIZARD_TEXT,
                'Database type'
            ),
            new TextConfigOption(
                self::INPUT_KEY_DB_INIT_STATEMENTS,
                TextConfigOption::FRONTEND_WIZARD_TEXT,
                'Database  initial set of commands'
            ),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function createConfig(array $data)
    {
        $configData = [];
        $configData[] = $this->configDataGenerator->createInstallConfig();
        $configData[] = $this->configDataGenerator->createCryptConfig($data);
        $modulesConfig = $this->configDataGenerator->createModuleConfig();
        if (isset($modulesConfig)) {
            $configData[] = $modulesConfig;
        }
        $configData[] = $this->configDataGenerator->createSessionConfig($data);
        $definitionConfig = $this->configDataGenerator->createDefinitionsConfig($data);
        if (isset($definitionConfig)) {
            $configData[] = $definitionConfig;
        }
        $configData[] = $this->configDataGenerator->createDbConfig($data);
        $configData[] = $this->configDataGenerator->createResourceConfig();
        return $configData;
    }

    /**
     * {@inheritdoc}
     */
    public function validate(array $options)
    {
        return [];
    }
}
