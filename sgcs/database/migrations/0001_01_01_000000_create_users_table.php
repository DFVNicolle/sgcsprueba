<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('usuarios', function (Blueprint $table) {
            $table->char('id', 36)->primary();
            $table->string('correo', 255)->unique();
            $table->timestamp('correo_verificado_en')->nullable();
            $table->string('nombre_completo', 255);
            $table->string('contrasena_hash', 255);
            $table->boolean('activo')->default(true);
            $table->rememberToken();
            $table->string('google2fa_secret', 255)->nullable();
            $table->timestamp('creado_en')->useCurrent();
            $table->timestamp('actualizado_en')->useCurrent()->useCurrentOnUpdate();
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->char('usuario_id', 36)->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });

        Schema::create('accesos', function (Blueprint $table) {
            $table->id();
            $table->char('usuario_id', 36);
            $table->string('ip', 50)->nullable();
            $table->string('accion', 100)->nullable();
            $table->string('recurso', 255)->nullable();
            $table->timestamp('creado_en')->useCurrent();
            $table->foreign('usuario_id')->references('id')->on('usuarios');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('accesos');
        Schema::dropIfExists('sessions');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('usuarios');
    }
};
