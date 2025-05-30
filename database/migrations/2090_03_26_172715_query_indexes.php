<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void {
        // Antes de crear índices sobre 'users', verifica si la tabla existe
        if (Schema::hasTable('users')) {
            Schema::table('users', function (Blueprint $table) {
                $table->index('email', 'idx_usuarios_email');
                $table->index('user_rol', 'idx_usuarios_tipo_usuario');
            });
        }

        // Antes de crear índices sobre 'professional_profiles', verifica si la tabla existe
        if (Schema::hasTable('professional_profiles')) {
            Schema::table('professional_profiles', function (Blueprint $table) {
                $table->index('user_id', 'idx_perfiles_profesionales_usuario_id');
            });
        }

        // Antes de crear índices sobre 'professional_specialities', verifica si la tabla existe
        if (Schema::hasTable('professional_specialities')) {
            Schema::table('professional_specialities', function (Blueprint $table) {
                $table->index('speciality_id', 'idx_profesional_especialidades_especialidad_id');
            });
        }

        // Antes de crear índices sobre 'work_hours', verifica si la tabla existe
        if (Schema::hasTable('work_hours')) {
            Schema::table('work_hours', function (Blueprint $table) {
                $table->index('professional_profile_id', 'idx_horas_trabajo_perfil_profesional_id');
                $table->index('day_of_week', 'idx_horas_trabajo_dia_semana');
            });
        }

        // Antes de crear índices sobre 'patient_profiles', verifica si la tabla existe
        if (Schema::hasTable('patient_profiles')) {
            Schema::table('patient_profiles', function (Blueprint $table) {
                $table->index('user_id', 'idx_perfiles_pacientes_usuario_id');
            });
        }

        // Antes de crear índices sobre 'subscriptions', verifica si la tabla existe
        if (Schema::hasTable('subscriptions')) {
            Schema::table('subscriptions', function (Blueprint $table) {
//            $table->index('user_id', 'idx_suscripciones_usuario_id');
                $table->index('subscription_status', 'idx_suscripciones_estado');
                $table->index('end_date', 'idx_suscripciones_fecha_fin');
            });
        }

        // Antes de crear índices sobre 'appointments', verifica si la tabla existe
        if (Schema::hasTable('appointments')) {
            Schema::table('appointments', function (Blueprint $table) {
                $table->index('appointment_date', 'idx_citas_fecha_hora_cita');
                $table->index('appointment_status', 'idx_citas_estado');
            });
        }

        // Antes de crear índices sobre 'reviews', verifica si la tabla existe
        if (Schema::hasTable('reviews')) {
            Schema::table('reviews', function (Blueprint $table) {
                $table->index('appointment_id', 'idx_resenas_cita_id');
                $table->index('rating', 'idx_resenas_calificacion');
            });
        }

        // Antes de crear índices sobre 'profile_views', verifica si la tabla existe
        if (Schema::hasTable('profile_views')) {
            Schema::table('profile_views', function (Blueprint $table) {
                $table->index('professional_profile_id', 'idx_vistas_perfil_perfil_profesional_id');
                $table->index('visitor_user_id', 'idx_vistas_perfil_visitante_usuario_id');
                $table->index('view_datetime', 'idx_vistas_perfil_fecha_hora_vista');
            });
        }

        // Antes de crear índices sobre 'medications', verifica si la tabla existe
        if (Schema::hasTable('medications')) {
            Schema::table('medications', function (Blueprint $table) {
                $table->index('name', 'idx_medicamentos_nombre');
            });
        }

        // Antes de crear índices sobre 'health_tips', verifica si la tabla existe
        if (Schema::hasTable('health_tips')) {
            Schema::table('health_tips', function (Blueprint $table) {
                $table->index('category_id', 'idx_consejos_salud_categoria');
            });
        }

        /*Schema::table('conversation_participants', function (Blueprint $table) {
            $table->index('user_id', 'idx_participantes_conversacion_usuario_id');
        });*/

        /*Schema::table('messages', function (Blueprint $table) {
            $table->index('conversation_id', 'idx_mensajes_conversacion_id');
            $table->index('sender_user_id', 'idx_mensajes_remitente_usuario_id');
            $table->index('sent_at', 'idx_mensajes_enviado_en');
        });*/
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        if (Schema::hasTable('users')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropIndex('idx_usuarios_email');
                $table->dropIndex('idx_usuarios_tipo_usuario');
            });
        }
        if (Schema::hasTable('professional_profiles')) {
            Schema::table('professional_profiles', function (Blueprint $table) {
                $table->dropIndex('idx_perfiles_profesionales_usuario_id');
//                $table->dropIndex('idx_perfiles_profesionales_departamento');
            });
        }
        if (Schema::hasTable('professional_specialties')) {
            Schema::table('professional_specialties', function (Blueprint $table) {
                $table->dropIndex('idx_profesional_especialidades_especialidad_id');
            });
        }
        if (Schema::hasTable('work_hours')) {
            Schema::table('work_hours', function (Blueprint $table) {
                $table->dropIndex('idx_horas_trabajo_perfil_profesional_id');
                $table->dropIndex('idx_horas_trabajo_dia_semana');
            });
        }
        if (Schema::hasTable('patient_profiles')) {
            Schema::table('patient_profiles', function (Blueprint $table) {
                $table->dropIndex('idx_perfiles_pacientes_usuario_id');
            });
        }
        if (Schema::hasTable('subscriptions')) {
            Schema::table('subscriptions', function (Blueprint $table) {
//            $table->dropIndex('idx_suscripciones_usuario_id');
                $table->dropIndex('idx_suscripciones_estado');
                $table->dropIndex('idx_suscripciones_fecha_fin');
            });
        }
        if (Schema::hasTable('appointments')) {
            Schema::table('appointments', function (Blueprint $table) {
                $table->dropIndex('idx_citas_fecha_hora_cita');
                $table->dropIndex('idx_citas_estado');
            });
        }
        if (Schema::hasTable('reviews')) {
            Schema::table('reviews', function (Blueprint $table) {
                $table->dropIndex('idx_resenas_cita_id');
                $table->dropIndex('idx_resenas_calificacion');
            });
        }
        if (Schema::hasTable('profile_views')) {
            Schema::table('profile_views', function (Blueprint $table) {
                $table->dropIndex('idx_vistas_perfil_perfil_profesional_id');
                $table->dropIndex('idx_vistas_perfil_visitante_usuario_id');
                $table->dropIndex('idx_vistas_perfil_fecha_hora_vista');
            });
        }
        if (Schema::hasTable('medications')) {
            Schema::table('medications', function (Blueprint $table) {
                $table->dropIndex('idx_medicamentos_nombre');
            });
        }
        if (Schema::hasTable('health_tips')) {
            Schema::table('health_tips', function (Blueprint $table) {
                $table->dropIndex('idx_consejos_salud_categoria');
            });
        }
        /*Schema::table('conversation_participants', function (Blueprint $table) {
            $table->dropIndex('idx_participantes_conversacion_usuario_id');
        });*/

        /*Schema::table('messages', function (Blueprint $table) {
            $table->dropIndex('idx_mensajes_conversacion_id');
            $table->dropIndex('idx_mensajes_remitente_usuario_id');
            $table->dropIndex('idx_mensajes_enviado_en');
        });*/
    }
};
