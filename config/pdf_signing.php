<?php

return [
	'form_permohonan' => [
		// Offset koreksi dalam persen dari lebar/tinggi halaman PDF
		// Nilai positif X menggeser ke kanan, nilai positif Y menggeser ke bawah
		'x_offset_percent' => env('FORM_TTD_X_OFFSET_PCT', 0),
		'y_offset_percent' => env('FORM_TTD_Y_OFFSET_PCT', 0),
	],
];


