<?php
/**
 * Created by PhpStorm.
 * User: Dmitry Loshmanov
 * Date: 19.05.2020
 * Time: 15:54
 */

namespace Common\Base\Repositories\Kafka;

use Exception;
use LogicException;

use RdKafka\Conf;
use RdKafka\Producer;

/**
 * Trait KafkaRepositoryTrait
 * @package Common\Repositories\Traits
 */
trait KafkaRepositoryTrait
{
    /**
     * @var Conf
     */
    protected $configuration;

    /**
     * @var string
     */
    protected $brokers;

    /**
     * Таймаут в мс при передаче сообщения
     * @var int
     */
    protected $timeout = 1000;

    /**
     * Топик для отправки сообщений
     * @var string
     */
    protected $topic;

    /**
     * KafkaRepositoryTrait constructor.
     * @throws Exception
     */
    public function __construct()
    {
        $this->configure();
    }

    /**
     * @throws Exception
     */
    protected function configure() {
        if (empty($this->connection)) {
            throw new LogicException('missing kafka connection for ' . self::class);
        }

        $raw = config("kafka.connections.{$this->connection}");
        if (empty($raw) || !array_key_exists('brokers', $raw)) {
            throw new LogicException("bad kafka configuration for {$this->connection}");
        }

        $brokers = $raw['brokers'];
        if (empty($brokers)) {
            throw new LogicException("missing kafka brokers for {$this->connection}");
        }

        $configuration = new Conf();
        if (array_key_exists('properties', $raw)) {
            foreach ($raw['properties'] as $name => $value) {
                $configuration->set($name, $value);
            }
        }

        if (array_key_exists('timeout', $raw)) {
            $this->timeout = $raw['timeout'];
        }

        if (array_key_exists('topic', $raw)) {
            $this->topic = $raw['topic'];
        }

        $this->configuration = $configuration;
        $this->brokers = $brokers;
    }

    /**
     * @return Producer
     */
    protected function producer(): Producer {
        $producer = new Producer($this->configuration);
        $producer->addBrokers($this->brokers);

        return $producer;
    }

    /**
     * @param string $payload
     * @param int $partition
     * @param int $flags
     * @throws Exception
     */
    protected function produce(string $payload, int $partition = RD_KAFKA_PARTITION_UA, int $flags = 0) {
        $producer = $this->producer();
        $producer->newTopic($this->topic)->produce($partition, $flags, $payload);

        $result = $producer->flush($this->timeout);
        if ($result === RD_KAFKA_RESP_ERR_NO_ERROR) {
            return;
        }

        switch ($result) {
            case RD_KAFKA_RESP_ERR__TIMED_OUT:
                throw \Common\Base\Exception\Exception::serviceUnavailable("timeout request to kafka ({$result})");
            default:
                throw \Common\Base\Exception\Exception::internalServerError("error request to kafka ({$result})");
        }
    }
}
