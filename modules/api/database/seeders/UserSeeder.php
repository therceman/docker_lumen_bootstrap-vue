<?php

namespace Database\Seeders;

use App\Model\UserModel;
use Illuminate\Database\Seeder;
use Ramsey\Uuid\Uuid;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeders.
     *
     * @return void
     */
    public function run()
    {
        UserModel::getQueryBuilder()->insert([
            // todo
        ]);
    }
}