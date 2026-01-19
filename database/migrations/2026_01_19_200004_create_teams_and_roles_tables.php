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
        // teams - الفرق
        Schema::create('teams', function (Blueprint $table) {
            $table->id();
            $table->foreignId('owner_id')->constrained('users')->cascadeOnDelete();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('type')->default('business'); // business, agency
            $table->string('logo')->nullable();
            $table->json('settings')->nullable();
            $table->timestamps();
        });

        // team_members - أعضاء الفريق
        Schema::create('team_members', function (Blueprint $table) {
            $table->id();
            $table->foreignId('team_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('role'); // owner, admin, editor, viewer
            $table->json('permissions')->nullable();
            $table->timestamp('joined_at')->useCurrent();
            $table->timestamps();
            $table->unique(['team_id', 'user_id']);
        });

        // team_invitations - دعوات الفريق
        Schema::create('team_invitations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('team_id')->constrained()->cascadeOnDelete();
            $table->foreignId('invited_by')->constrained('users')->cascadeOnDelete();
            $table->string('email');
            $table->string('role')->default('editor');
            $table->string('token')->unique();
            $table->timestamp('expires_at');
            $table->timestamp('accepted_at')->nullable();
            $table->timestamps();
        });

        // agency_clients - عملاء الوكالة
        Schema::create('agency_clients', function (Blueprint $table) {
            $table->id();
            $table->foreignId('agency_team_id')->constrained('teams')->cascadeOnDelete();
            $table->foreignId('client_user_id')->nullable()->constrained('users')->nullOnDelete();
            
            $table->string('name');
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('company')->nullable();
            $table->text('notes')->nullable();
            
            $table->string('status')->default('active'); // active, inactive, archived
            $table->json('access_permissions')->nullable();
            
            $table->timestamps();
        });

        // تحديث users table
        Schema::table('users', function (Blueprint $table) {
            $table->string('account_type')->default('individual')->after('role'); // individual, business, agency, student
            $table->foreignId('current_team_id')->nullable()->after('account_type')->constrained('teams')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['current_team_id']);
            $table->dropColumn(['account_type', 'current_team_id']);
        });
        
        Schema::dropIfExists('agency_clients');
        Schema::dropIfExists('team_invitations');
        Schema::dropIfExists('team_members');
        Schema::dropIfExists('teams');
    }
};
