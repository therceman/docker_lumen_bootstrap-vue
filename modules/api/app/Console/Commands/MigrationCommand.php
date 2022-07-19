<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class MigrationCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'migration:create {model}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create migration file from model';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle(): void
    {
        $migrations_path = storage_path('../database/migrations');

        $model = $this->getModelName();
        $table = $this->getTableName($model);
        $tpl = $this->createTpl($model);
        $date = $this->getDateStr();

        file_put_contents($migrations_path . '/' . $date . '_create_' . $table . '_table.php', $tpl);
    }

    public function getDateStr(): string
    {
        $date = date("Y") . '_' . date('m') . '_' . date('d');
        $time = date('H') . date('i') . date('s');

        return $date . '_' . $time;
    }

    public function getModelName(): string
    {
        $model = ucfirst($this->argument('model'));

        return strpos($model, 'Model') === false ? $model . 'Model' : $model;
    }

    public function getTableName($model): string
    {
        $table = strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $model));

        return str_replace('_model', '', $table);
    }

    public function createTpl($model): string
    {
        $tableName = ucfirst(str_replace('Model', '', $model));

        return <<<XML
<?php

use App\Model\\${model};
use Illuminate\Database\Migrations\Migration;

class Create${tableName}Table extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        ${model}::createMigrationSchema();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        ${model}::dropMigrationSchema();
    }
}
XML;
    }
}