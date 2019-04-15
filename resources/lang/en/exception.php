<?php

return 
	
	[
		\App\Exceptions\AppException::ERR_NONE => 'Thành công',
		\App\Exceptions\AppException::ERR_ACCOUNT_NOT_FOUND => 'Không tìm thấy tài khoản',
		\App\Exceptions\AppException::ERR_SYSTEM => 'Lỗi hệ thống',
		\App\Exceptions\AppException::ERR_NOT_USER => 'Không tìm thấy người dùng',
		\App\Exceptions\AppException::ERR_INVALID_TOKEN => 'Hết phiên đăng nhập',
	];
