<?php

namespace Infrastructure;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Migration extends Command
{
    protected static $defaultName = 'migration';

    protected function configure()
    {
        $this
            ->setDescription('Create database tables')
            ->setHelp('This command allows you to migration the database.')
            ->addArgument('type', InputArgument::REQUIRED, 'Type of migration');

        parent::configure();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln([
            'Migration',
            '============',
        ]);

        $database = new \Infrastructure\Database();

        if ($input->getArgument('type') === 'up') {
            $database->getClient()->getSchemaBuilder()->create('products', function ($table) {
                $table->increments('id');
                $table->string('name');
                $table->unsignedInteger('quantity');
                $table->unsignedInteger('price');
                $table->timestamps();
            });

            $database->getClient()->getSchemaBuilder()->create('users', function ($table) {
                $table->increments('id');
                $table->string('username')->unique();
                $table->string('password');
                $table->timestamps();
            });

            $database->getClient()->getSchemaBuilder()->create('carts', function ($table) {
                $table->increments('id');
                $table->unsignedInteger('user_id');
                $table->timestamps();
            });

            $database->getClient()->getSchemaBuilder()->create('cart_items', function ($table) {
                $table->increments('id');
                $table->unsignedInteger('cart_id');
                $table->unsignedInteger('product_id');
                $table->unsignedInteger('quantity');
                $table->timestamps();
            });
        }

        if ($input->getArgument('type') === 'down') {
            $database->getClient()->getSchemaBuilder()->drop("products");
            $database->getClient()->getSchemaBuilder()->drop("users");
            $database->getClient()->getSchemaBuilder()->drop("carts");
            $database->getClient()->getSchemaBuilder()->drop("cart_items");
        }

        $output->writeLn([
            'Successfully migrated',
        ]);

        return Command::SUCCESS;
    }
}