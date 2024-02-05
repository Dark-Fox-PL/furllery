<?php

class Furllery {
	public function __construct() {
		return $this->initialize_plugin();
	}

	protected function initialize_plugin(): Furllery {
		return $this->maybe_run_admin()->run_plugin();
	}

	protected function run_plugin(): Furllery {
		return $this;
	}

	protected function maybe_run_admin(): Furllery {
		if (!is_admin()) {
			return $this;
		}

		$admin = new FurlleryAdmin;
		$admin->initialize();

		return $this;
	}

}