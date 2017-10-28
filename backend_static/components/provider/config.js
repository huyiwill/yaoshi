angular
	.module('ohConfig', [])
	.provider('$config', function $configProvider() {
		var config = {
			"api_uri": "http://zhsy_dashboard.shnow.cn",
			"default_lat": 40.7167,
			"default_long": -74
		}

		this.$get = function () {
			return config
		}
	})