<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ElementoConfiguracion;
use App\Models\Proyecto;

class ElementoConfiguracionSeeder extends Seeder
{
    public function run(): void
    {
        $proyecto = Proyecto::where('nombre', 'Proyecto Demo')->first();
        if (!$proyecto) return;

        // Elementos de configuración para App de Pedidos
        $elementos = [
            ['titulo' => 'Documento de Requisitos', 'descripcion' => 'Documento que describe los requisitos funcionales y no funcionales', 'tipo' => 'DOCUMENTO'],
            ['titulo' => 'SAD', 'descripcion' => 'Especificación de Arquitectura de Software', 'tipo' => 'DOCUMENTO'],
            ['titulo' => 'SRC', 'descripcion' => 'Especificación de Requisitos de Software', 'tipo' => 'DOCUMENTO'],
            ['titulo' => 'Manual de Usuario', 'descripcion' => 'Guía para el usuario final', 'tipo' => 'DOCUMENTO'],
            ['titulo' => 'RF-01: Gestión de Pedidos', 'descripcion' => 'Requerimiento funcional para gestionar pedidos', 'tipo' => 'DOCUMENTO'],
            ['titulo' => 'RF-02: Gestión de Usuarios', 'descripcion' => 'Requerimiento funcional para gestionar usuarios', 'tipo' => 'DOCUMENTO'],
            ['titulo' => 'RF-03: Gestión de Notificaciones', 'descripcion' => 'Requerimiento funcional para gestionar notificaciones', 'tipo' => 'DOCUMENTO'],
            ['titulo' => 'pedidos.dart', 'descripcion' => 'Módulo principal para gestión de pedidos', 'tipo' => 'CODIGO'],
            ['titulo' => 'editar.dart', 'descripcion' => 'Módulo para edición de usuarios', 'tipo' => 'CODIGO'],
            ['titulo' => 'notificaciones.dart', 'descripcion' => 'Módulo para gestión de notificaciones', 'tipo' => 'CODIGO'],
            ['titulo' => 'script_bd_pedidos.sql', 'descripcion' => 'Script de base de datos para pedidos', 'tipo' => 'SCRIPT_BD'],
            ['titulo' => 'script_bd_usuarios.sql', 'descripcion' => 'Script de base de datos para usuarios', 'tipo' => 'SCRIPT_BD'],
            ['titulo' => 'script_bd_notificaciones.sql', 'descripcion' => 'Script de base de datos para notificaciones', 'tipo' => 'SCRIPT_BD'],
        ];
        $ids = [];
        foreach ($elementos as $e) {
            $elemento = ElementoConfiguracion::create([
                'titulo' => $e['titulo'],
                'descripcion' => $e['descripcion'],
                'proyecto_id' => $proyecto->id,
                'tipo' => $e['tipo'],
            ]);
            $ids[$e['titulo']] = $elemento->id;
        }

        // Relaciones realistas para App de Pedidos
        // Documento de Requisitos -> SAD
        \App\Models\RelacionEC::create([
            'desde_ec' => $ids['Documento de Requisitos'],
            'hacia_ec' => $ids['SAD'],
            'tipo_relacion' => 'DERIVADO_DE',
            'nota' => 'El documento de requisitos deriva de la arquitectura SAD',
        ]);
        // SAD -> SRC
        \App\Models\RelacionEC::create([
            'desde_ec' => $ids['SAD'],
            'hacia_ec' => $ids['SRC'],
            'tipo_relacion' => 'DERIVADO_DE',
            'nota' => 'La arquitectura SAD deriva de los requisitos SRC',
        ]);
        // Manual de Usuario -> SRC
        \App\Models\RelacionEC::create([
            'desde_ec' => $ids['Manual de Usuario'],
            'hacia_ec' => $ids['SRC'],
            'tipo_relacion' => 'REFERENCIA',
            'nota' => 'El manual de usuario referencia los requisitos SRC',
        ]);
        // RF dependen de SAD y SRC
        foreach(['RF-01: Gestión de Pedidos', 'RF-02: Gestión de Usuarios', 'RF-03: Gestión de Notificaciones'] as $rf) {
            \App\Models\RelacionEC::create([
                'desde_ec' => $ids[$rf],
                'hacia_ec' => $ids['SAD'],
                'tipo_relacion' => 'DEPENDE_DE',
                'nota' => 'El requerimiento depende de la arquitectura SAD',
            ]);
            \App\Models\RelacionEC::create([
                'desde_ec' => $ids[$rf],
                'hacia_ec' => $ids['SRC'],
                'tipo_relacion' => 'DEPENDE_DE',
                'nota' => 'El requerimiento depende de los requisitos SRC',
            ]);
        }
        // Módulos dependen de sus RF
        \App\Models\RelacionEC::create([
            'desde_ec' => $ids['pedidos.dart'],
            'hacia_ec' => $ids['RF-01: Gestión de Pedidos'],
            'tipo_relacion' => 'DEPENDE_DE',
            'nota' => 'El módulo pedidos.dart depende del RF de pedidos',
        ]);
        \App\Models\RelacionEC::create([
            'desde_ec' => $ids['editar.dart'],
            'hacia_ec' => $ids['RF-02: Gestión de Usuarios'],
            'tipo_relacion' => 'DEPENDE_DE',
            'nota' => 'El módulo editar.dart depende del RF de usuarios',
        ]);
        \App\Models\RelacionEC::create([
            'desde_ec' => $ids['notificaciones.dart'],
            'hacia_ec' => $ids['SRC'],
            'tipo_relacion' => 'DEPENDE_DE',
            'nota' => 'El módulo notificaciones.dart depende de los requisitos de notificaciones',
        ]);
        // Scripts BD dependen de módulos
        \App\Models\RelacionEC::create([
            'desde_ec' => $ids['script_bd_pedidos.sql'],
            'hacia_ec' => $ids['pedidos.dart'],
            'tipo_relacion' => 'DERIVADO_DE',
            'nota' => 'El script de BD de pedidos deriva del módulo pedidos.dart',
        ]);
        \App\Models\RelacionEC::create([
            'desde_ec' => $ids['script_bd_usuarios.sql'],
            'hacia_ec' => $ids['editar.dart'],
            'tipo_relacion' => 'DERIVADO_DE',
            'nota' => 'El script de BD de usuarios deriva del módulo editar.dart',
        ]);
        \App\Models\RelacionEC::create([
            'desde_ec' => $ids['script_bd_notificaciones.sql'],
            'hacia_ec' => $ids['notificaciones.dart'],
            'tipo_relacion' => 'DERIVADO_DE',
            'nota' => 'El script de BD de notificaciones deriva del módulo notificaciones.dart',
        ]);
    }
}
