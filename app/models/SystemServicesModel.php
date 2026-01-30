<?php

class SystemServicesModel extends Model
{
    protected string $table = 'system_services';

    public function allWithType(int $companyId): array
    {
        return $this->db->fetchAll(
            'SELECT system_services.*, service_types.name as type_name
             FROM system_services
             JOIN service_types ON system_services.service_type_id = service_types.id
             WHERE system_services.company_id = :company_id AND service_types.company_id = :company_id
             ORDER BY system_services.id DESC',
            ['company_id' => $companyId]
        );
    }

    public function popularHostingAndDomain(int $companyId, int $limit = 10): array
    {
        $limit = max(1, (int)$limit);
        return $this->db->fetchAll(
            'SELECT system_services.*, service_types.name as type_name
             FROM system_services
             JOIN service_types ON system_services.service_type_id = service_types.id
             WHERE system_services.company_id = :company_id
               AND service_types.company_id = :company_id
               AND LOWER(service_types.name) IN ("hosting", "dominio")
             ORDER BY system_services.id DESC
             LIMIT ' . $limit,
            ['company_id' => $companyId]
        );
    }
}
