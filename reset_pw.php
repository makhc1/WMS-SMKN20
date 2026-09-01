<?php
App\Models\User::where('email', 'ahmad123@wms.id')->update(['password' => Hash::make('password123')]);
echo "Ahmad password reset to 'password123'\n";
