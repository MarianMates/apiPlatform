<?php
declare(strict_types=1);
namespace App\Command;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Contracts\Cache\CacheInterface;
#[AsCommand(name: 'app:test-redis')]
final class TestRedisCommand extends Command
{
    public function __construct(private readonly CacheInterface $cache)
    {
        parent::__construct();
    }
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        // Fetch from cache or set if not exists
        $cacheItem = $this->cache->getItem('test_key');
        if (!$cacheItem->isHit()) {
            $cacheItem->set('Hello Redis!');
            $this->cache->save($cacheItem);
        }
        $output->writeln('Value from Redis: ' . $cacheItem->get());
        return Command::SUCCESS;
    }
}