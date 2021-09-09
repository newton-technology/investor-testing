<?php

return [
    'connections' => [
        'analytics' => [
            // несколько брокеров можно указать через запятую, например: 10.0.2.10:9092,10.0.2.11:9092
            'brokers' => env('ANALYTICS_KAFKA_BROKERS', '10.0.2.10:9092'),
            // таймаут публикации сообщений в миллисекундах
            'timeout' => env('ANALYTICS_KAFKA_TIMEOUT', 1000),
            // топик для публикации сообщений
            'topic' => env('ANALYTICS_KAFKA_TOPIC', 'analytics'),
        ],
        'application_log' => [
            // несколько брокеров можно указать через запятую, например: 10.0.2.10:9092,10.0.2.11:9092
            'brokers' => env('APPLICATION_LOG_KAFKA_BROKERS', 'kafka:9092'),
            // таймаут публикации сообщений в миллисекундах
            'timeout' => env('APPLICATION_LOG_KAFKA_TIMEOUT', 10000),
            // топик для публикации сообщений
            'topic' => env('APPLICATION_LOG_KAFKA_TOPIC', 'application_log'),
        ],
    ],
];
