<?php
/**
 * Created by PhpStorm.
 * User: dloshmanov
 * Date: 01.12.2017
 * Time: 13:41
 */

namespace Common\Base\Illuminate\Console;

use Exception;

/**
 * Trait DbPrivilegedCommandTrait
 *
 * Трейт для создания привилегированных подключений из консольной команды.
 *
 * Для использования привилегированных подключений необходимо в конструкторе консольной команды
 * вызвать конструктор трейта до вызова parent::__construct(). Это добавит необходимые опции командной строки.
 *
 * Дальше в методе handle() консольной команды необходимо вызвать метод трейта setConnectionParameters(). Этот метод
 * создает привилегированные виртуальные подключения к базам данных на основе существующих подключений.
 *
 * Виртуальные подключения создаются на основе существующих подключений из конфигурации приложения. Имя пользователя
 * и пароль задаются в консольной команде. Чтобы получить имя привилегированного подключения в консольной команде,
 * использующей этот трейт, следует использовать метод getPrivelegedConnectionName().
 *
 * @package Terminal\Console\Commands\Database
 */
trait DBPrivilegedCommandTrait
{
    /**
     * @var string
     */
    private $__signature = '{--D|database=* : Database for migrations} '
    . '{--U|username= : User for migrations} '
    . '{--P|password= : User password for migrations}'
    . '{--recursive : Run command for all connrctions}';

    /**
     * Список привилегированных подключений
     * @var
     */
    private $connectionList;

    /**
     * DbPrivilegedCommandTrait constructor.
     */
    protected function __construct()
    {
        $this->signature .= $this->__signature;
    }

    /**
     * @throws Exception
     */
    private function setConnectionParameters()
    {
        $this->setConnectionList();
        $this->setUsername();
        $this->setPassword();

        $this->makeConnections();
    }

    /**
     * Make virtual connections
     */
    private function makeConnections() {
        foreach ($this->connectionList as $name => $config) {
            foreach ($config as $option => $value) {
                config([
                    "database.connections.{$name}.{$option}" => $value,
                ]);
            }
        }
    }

    /**
     * @param $connectionName
     * @return string
     * @throws Exception
     */
    private function getPrivelegedConnectionName($connectionName) {
        if (empty($connectionName)) {
            throw new Exception('Connection name is empty');
        }
        return '__privileged__' . $connectionName;
    }

    /**
     * @throws Exception
     */
    private function setConnectionList() {
        $connections = $this->option('recursive') ?
            array_keys(config('database.connections')) : $this->option('database');

        $connectionList = [];
        foreach ($connections as $connectionName) {

            $connectionConfig = config("database.connections.{$connectionName}");
            if (empty($connectionConfig)) {
                $errorMessage = 'No settings found for connecting to "' . $connectionName . '"';
                throw new Exception($errorMessage);
            }

            $connectionList[$this->getPrivelegedConnectionName($connectionName)] = $connectionConfig;
        }

        if (empty($connectionList)) {
            throw new Exception('Database is not set. Use --database option to set it.');
        }

        $this->connectionList = $connectionList;
    }

    /**
     * @throws Exception
     */
    private function setUsername() {
        $username = $this->option('username');
        if (empty($username)) {
            throw new Exception('Username is not set. Use --username option to set it.');
        }

        $this->updateConnectionsConfig('username', $username);
    }

    /**
     * @throws Exception
     */
    private function setPassword() {
        $password = $this->option('password');
        if (empty($password)) {
            $password = $this->secret("What is password for {$this->option('username')}?");
        }

        if (empty($password)) {
            throw new Exception('Password is not set. Use --password option to set it (or enter nonempty password).');
        }

        $this->updateConnectionsConfig('password', $password);
    }

    /**
     * @param $optionName
     * @param $newValue
     */
    private function updateConnectionsConfig($optionName, $newValue) {
        foreach ($this->connectionList as $name => $config) {
            if (array_key_exists($optionName, $config)) {
                $this->connectionList[$name][$optionName] = $newValue;
            }
        }
    }

}
