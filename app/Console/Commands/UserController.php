<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

class UserController extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'users:create {email} {password} {--api}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $user = User::where('email',$this->argument('email'))->first();
        if (!$user) {
            User::insert([
                'name' => $this->argument('email'),
                'email' => $this->argument('email'),
                'email_verified_at' => now(),
                'password' => Hash::make($this->argument('password')), // password
                'remember_token' => Str::random(10),
            ]);
            $this->info('User created');
        } else {
            $user->password = Hash::make($this->argument('password'));
            $user->save();
            $this->info('User\'s password changed');
        }
        if ($this->option('api')) {
            $user = User::where('email',$this->argument('email'))->first();
            $token = $user->createToken($user->name);
            $this->info('User token: '.$token->plainTextToken);
        }
        return 0;
    }
}
