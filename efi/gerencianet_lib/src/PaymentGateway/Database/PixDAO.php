<?php

namespace PaymentGateway\Database;

use Illuminate\Database\Capsule\Manager as Capsule;
use PaymentGateway\Logging\TransactionLogger;

class PixDAO
{
    /**
     * Cria um novo registro de pix na tabela tblgerencianetpix.
     *
     * @param array $data Dados do pix para inserção.
     * @return bool Sucesso ou falha na criação.
     */
    public static function create(array $data): bool
    {
        try {
            return Capsule::table('tblgerencianetpix')->insert($data);
        } catch (\Throwable $th) {
            TransactionLogger::log(json_encode($th->getMessage()), TransactionLogger::ERROR_LOG);
            return false;
        }
    }

    /**
     * Busca um registro de boleto pelo invoiceid.
     *
     * @param int $invoiceid ID da fatura (invoiceid).
     * @return object|null Registro encontrado ou null se não existir.
     */
    public static function findByInvoiceId(int $invoiceid): ?object
    {
        try {
            return Capsule::table('tblgerencianetpix')->where('invoiceid', $invoiceid)->first();
        } catch (\Throwable $th) {
            TransactionLogger::log(json_encode($th->getMessage()), TransactionLogger::ERROR_LOG);
            return null;
        }
    }



    /**
     * Atualiza um registro de pix pelo charge_id.
     *
     * @param int $charge_id ID da cobrança (charge_id).
     * @param array $data Dados atualizados para o pix.
     * @return bool Sucesso ou falha na atualização.
     */
    public static function update(int $charge_id, array $data): bool
    {
        try {
            return Capsule::table('tblgerencianetpix')
                ->where('charge_id', $charge_id)
                ->update($data) > 0;
        } catch (\Throwable $th) {
            TransactionLogger::log(json_encode($th->getMessage()), TransactionLogger::ERROR_LOG);
            return false;
        }
    }
}
