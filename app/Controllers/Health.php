<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\Database\Exceptions\DatabaseException;
use CodeIgniter\HTTP\ResponseInterface;

final class Health extends Controller
{
    /** Liveness deliberately does not initialize a database connection. */
    public function live(): ResponseInterface
    {
        return $this->response->setStatusCode(204);
    }

    /** Readiness performs only a bounded, read-only probe and exposes no DB detail. */
    public function ready(): ResponseInterface
    {
        try {
            $db = \Config\Database::connect();
            $row = $db->query('SELECT 1 AS ready')->getRowArray();
            if (($row['ready'] ?? null) !== 1 && ($row['ready'] ?? null) !== '1') {
                return $this->response->setStatusCode(503)->setJSON(['status' => 'unavailable']);
            }
        } catch (DatabaseException $exception) {
            return $this->response->setStatusCode(503)->setJSON(['status' => 'unavailable']);
        }

        return $this->response->setJSON(['status' => 'ok']);
    }
}
