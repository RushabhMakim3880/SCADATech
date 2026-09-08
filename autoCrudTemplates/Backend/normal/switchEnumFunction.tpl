<?php
public function switch{{UC_FIELD_NAME}}(${{PRIMARY_FIELD}},$value)
{
    ${{PRIMARY_FIELD}} = (int)getKey(${{PRIMARY_FIELD}}, "{{ITEM_NAME}}");

    if (!UserPermissionLib::userCanDo("{{MODULE_NAME}}", 'edit')) {
        return $this->failForbidden('Insufficient permissions');
    }

    if (${{PRIMARY_FIELD}} == 0) {
        return $this->fail('Invalid request', 400);
    }

    $this->db->table('{{TABLE_NAME}}')->set('{{FIELD_NAME}}', $value)->where('{{PRIMARY_FIELD}}', ${{PRIMARY_FIELD}})->update();

    $response = [
        'status' => true,
        "message" => "Done.",
    ];

    return $this->respond($response, 200);
}