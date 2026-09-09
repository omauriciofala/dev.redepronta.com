<?php

namespace App\Console\Commands;

use Database\Seeders\DummyPeopleSeeder;
use Illuminate\Console\Command;

class PopulateDummyPeopleCommand extends Command
{
    protected $signature = 'people:populate {--count=1000 : Quantidade de pessoas fictícias a popular}';
    protected $description = 'Popula cadastros fictícios de pessoas (PF e PJ) com papéis, dados e endereços para testes e inspeção';

    public function handle(): int
    {
        $count = (int) $this->option('count');
        if ($count <= 0) {
            $this->error('A quantidade deve ser maior que zero.');
            return Command::FAILURE;
        }

        $this->info("Iniciando população de {$count} pessoas fictícias...");
        $startTime = microtime(true);

        $seeder = new DummyPeopleSeeder();
        $seeder->setCommand($this);
        $seeder->run($count);

        $elapsed = round(microtime(true) - $startTime, 2);
        $this->info("Concluído com sucesso em {$elapsed} segundos!");

        return Command::SUCCESS;
    }
}
