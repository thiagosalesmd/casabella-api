<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PermissionsSeeders extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $permissions = [
            0 => [
                'name' => 'Patrocinador',
                'description' => 'visualização do cadastro de patrocinador',
                'module' => 'partner'
            ],
            1 => [
                'name' => 'Cadastro Patrocinador',
                'description' => 'Alteração no cadastro de patrocinador',
                'module' => 'partner'
            ],
            2 => [
                'name' => 'Solicitações',
                'description' => 'Acesso a solicitações',
                'module' => 'tickets'
            ],
            3 => [
                'name' => 'Chat',
                'description' => 'Acesso ao Chat',
                'module' => 'chat'
            ],
            4 => [
                'name' => 'Termos',
                'description' => 'Acesso ao cadastro de Termos',
                'module' => 'terms'
            ],
            5 => [
                'name' => 'Auditoria',
                'description' => 'Acesso ao cadastro de auditoria.',
                'module' => 'audit'
            ]
        ];

        foreach ($permissions as $item) {
            if (!$this->isExist($item['name'])) {
                Permission::create($item);
            }
        }
    }

    protected function isExist ($name)
    {
        $permission = Permission::where('name', $name)->count();
        if ($permission > 0) {
            return true;
        }
        return false;
    }
}
