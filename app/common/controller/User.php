<?php
	namespace app\common\controller;

	class User
	{
		public function showName($name = '')
		{
			return "my name is {$name}";
		}
	}