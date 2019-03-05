<?php

use Illuminate\Database\Seeder;

class InsoftTagsSeeder extends Seeder
{
    use \App\Traits\TagTrait;
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->fetchTags();
    }
}
