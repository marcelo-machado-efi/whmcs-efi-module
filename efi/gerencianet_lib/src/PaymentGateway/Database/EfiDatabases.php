<?php

namespace PaymentGateway\Database;

use Illuminate\Database\Capsule\Manager as Capsule;
use PaymentGateway\Logging\TransactionLogger;

/**
 * Classe responsável por gerenciar a criação de tabelas no banco de dados para o sistema de pagamento.
 */
class EfiDatabases
{
    /**
     * Cria todas as tabelas necessárias para o sistema.
     *
     * @return bool Retorna true se todas as tabelas foram criadas com sucesso, false caso contrário.
     */
    public static function create(): bool
    {
        try {
            $success = false;
            $success = self::createBoletoDatabase();
            $success = self::createPixDatabase();
            $success = self::createOFDatabase();

            return $success;
        } catch (\Throwable $th) {
            TransactionLogger::log(json_encode($th->getMessage()), TransactionLogger::ERROR_LOG);
            return false;
        }
    }

    /**
     * Cria a tabela `tblboletoefi` para armazenar dados de boletos.
     *
     * @return bool Retorna true se a tabela foi criada com sucesso, false caso contrário.
     */
    private static function createBoletoDatabase(): bool
    {
        $createTable = true;
        if (!Capsule::schema()->hasTable('tblboletoefi')) {
            $createTable = Capsule::schema()->create('tblboletoefi', function ($table) {
                $table->increments('id');
                $table->integer('charge_id')->unique();
                $table->unsignedInteger('invoiceid');
                $table->string('link_pdf', 10000);
                $table->foreign('invoiceid')->references('id')->on('tblinvoices')->onDelete('cascade');
            });
        }

        return $createTable;
    }

    /**
     * Cria a tabela `tblgerencianetpix` para armazenar dados de pagamentos via Pix.
     *
     * @return bool Retorna true se a tabela foi criada com sucesso, false caso contrário.
     */
    private static function createPixDatabase(): bool
    {
        $createTable = true;
        if (!Capsule::schema()->hasTable('tblgerencianetpix')) {
            $createTable = Capsule::schema()->create('tblgerencianetpix', function ($table) {
                $table->increments('id');
                $table->integer('invoiceid')->unique();
                $table->string('txid')->unique();
                $table->integer('locid');
                $table->string('e2eid');
            });
        }

        return $createTable;
    }

    /**
     * Cria a tabela `tblefiopenfinance` para armazenar dados relacionados ao Open Finance.
     *
     * @return bool Retorna true se a tabela foi criada com sucesso, false caso contrário.
     */
    private static function createOFDatabase(): bool
    {
        $createTable = true;
        if (!Capsule::schema()->hasTable('tblefiopenfinance')) {
            $createTable = Capsule::schema()->create('tblefiopenfinance', function ($table) {
                $table->integer('invoiceid')->unique()->primary();
                $table->string('identificadorPagamento')->unique();
                $table->string('e2eid');
            });
        }

        return $createTable;
    }
}
