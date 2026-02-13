<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class CreateUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:create-user';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Criação de usuario';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $name = $this->ask('Nome do usuario');
        $email = $this->ask('Email do usuario');
        $password = $this->secret('Senha do usuario');

        $user = User::create([
            'name' => $name,
            'email' => $email,
            'password' => $password,
        ]);

        $this->info("Usuario criado com sucesso!");
    }
}
