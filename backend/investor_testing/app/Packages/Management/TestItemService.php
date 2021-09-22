<?php
/**
 * Created by PhpStorm.
 * User: Dmitry Loshmanov
 * Date: 22.09.2021
 * Time: 10:31
 */

namespace Newton\InvestorTesting\Packages\Management;

use Throwable;

use Newton\InvestorTesting\Packages\Common\Exception\ExceptionWithoutReport;
use Newton\InvestorTesting\Packages\Common\TestItemFactory;
use Newton\InvestorTesting\Packages\Common\TestRepository;
use Newton\InvestorTesting\Packages\Common\UserRepository;

class TestItemService
{
    protected TestItemFactory $testItemFactory;
    protected TestRepository $testRepository;
    protected UserRepository $userRepository;

    public function __construct(
        TestRepository $testRepository,
        TestItemFactory $testItemFactory,
        UserRepository $userRepository
    ) {
        $this->testRepository = $testRepository;
        $this->userRepository = $userRepository;
        $this->testItemFactory = $testItemFactory;
    }

    /**
     * @throws Throwable
     */
    public function getTestById(int $id): TestItem
    {
        $test = $this->testRepository->getTestById($id);
        if (empty($test)) {
            throw ExceptionWithoutReport::testNotFound();
        }

        $raw = $this->testItemFactory->fromTest($test);
        $user = $this->userRepository->getUserById($test->getUserId());
        return (new TestItem())
            ->setUserId($test->getUserId())
            ->setUserEmail(empty($user) ? null : $user->getEmail())
            ->setId($raw->getId())
            ->setStatus($raw->getStatus())
            ->setCategory($raw->getCategory())
            ->setQuestions($raw->getQuestions())
            ->setCreatedAt($raw->getCreatedAt())
            ->setUpdatedAt($raw->getUpdatedAt());
    }
}
