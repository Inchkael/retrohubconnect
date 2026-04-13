<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            // Ajout des champs pour le nom et prénom
            $table->string('last_name', 100)->after('id');
            $table->string('first_name', 100)->after('last_name');

            // Ajout du pseudo
            $table->string('nickname', 100)->nullable()->after('email');

            // Rôle de l'utilisateur
            $table->enum('role', ['visitor', 'member', 'seller', 'admin'])->default('visitor')->after('password');

            // Langue préférée
            $table->string('language', 10)->default('fr')->after('remember_token');

            // Avatar
            $table->string('avatar', 255)->nullable()->after('language');

            // Adresse
            $table->string('zip', 20)->nullable()->after('avatar');
            $table->string('state', 100)->nullable()->after('zip');
            $table->string('address', 255)->nullable()->after('state');
            $table->string('address2', 255)->nullable()->after('address');
            $table->string('country', 100)->nullable()->after('address2');
            $table->string('city', 100)->nullable()->after('country');

            // Date de naissance
            $table->date('birth_date')->nullable()->after('city');

            // Statut du compte
            $table->boolean('enabled')->default(true)->after('birth_date');

            // Dernière connexion et tentatives de connexion
            $table->timestamp('last_login')->nullable()->after('enabled');
            $table->integer('login_attempts')->default(0)->after('last_login');

            // Verrouillage du compte
            $table->timestamp('locked_until')->nullable()->after('login_attempts');
            $table->foreignId('locked_by')->nullable()->after('locked_until')->constrained('users');
            $table->string('lock_reason', 255)->nullable()->after('locked_by');

            // Index pour optimiser les requêtes
            $table->index('role');
            $table->index('enabled');
            $table->index('last_login');
            $table->index('login_attempts');
            $table->index('locked_until');
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'last_name', 'first_name', 'nickname', 'role', 'language', 'avatar',
                'zip', 'state', 'address', 'address2', 'country', 'city', 'birth_date',
                'enabled', 'last_login', 'login_attempts', 'locked_until', 'locked_by', 'lock_reason'
            ]);
            $table->dropIndex(['users_role_index', 'users_enabled_index', 'users_last_login_index', 'users_login_attempts_index', 'users_locked_until_index']);
        });
    }
};
