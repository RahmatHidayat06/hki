<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
	public function up(): void
	{
		Schema::table('signatures', function (Blueprint $table) {
			$table->unsignedInteger('page')->nullable()->after('signature_token');
			$table->decimal('x_percent', 6, 2)->nullable()->after('page');
			$table->decimal('y_percent', 6, 2)->nullable()->after('x_percent');
			$table->decimal('width_percent', 6, 2)->nullable()->after('y_percent');
			$table->decimal('height_percent', 6, 2)->nullable()->after('width_percent');
		});
	}

	public function down(): void
	{
		Schema::table('signatures', function (Blueprint $table) {
			$table->dropColumn(['page', 'x_percent', 'y_percent', 'width_percent', 'height_percent']);
		});
	}
}; 